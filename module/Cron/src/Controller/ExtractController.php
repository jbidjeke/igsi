<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Cron\Controller;

use RuntimeException;
use Zend\Db\Adapter\AdapterInterface;
use Cron\Model\DbTable\EsmsApp;
use Cron\Model\DbTable;
use Application\Controller\BaseController;
use Zend\Console\Request as ConsoleRequest;
use Classes\Utile;

class ExtractController extends BaseController
{

    protected $db, $esmsApp, $igApp, $config, $calculDate, $igDashboard;

    public function __construct($config, AdapterInterface $db, DbTable\EsmsApp $esmsApp, DbTable\IgApp $igApp, Utile\CalculDate $calculDate, Utile\IgDashboard $igDashboard)
    {
        $this->config = $config;
        $this->db = $db;
        $this->esmsApp = $esmsApp;
        $this->igApp = $igApp;
        $this->calculDate = $calculDate;
        $this->igDashboard = $igDashboard;
    }

    public function indexAction()
    {
        echo "Successful!";
    }

    public function esmsechebdoAction()
    {
        $request = $this->getRequest();
        
        // Make sure that we are running in a console, and the user has not
        // tricked our application into running this action from a public web
        // server:
        if (! $request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }
        
        /* Fichier joint */
        $i = 0;
        $counter = 1;
        $path = $this->config["csv"]['dataout'];
        // $path = '';
        $extract = $path . 'ExtractESMSHebdo_S' . date("W") . '-' . date('w') . '.csv';
        
        try {
            $fp = fopen($extract, "w+");
        } catch (\Exception $e) {
            echo 'Ouverture du fichier ' . $extract . ' impossible.';
        }
        
        $textFile = "#;Prefix;No. Ticket;Nature;Domaine;REF APP;NOM APP;IG;Description;Date envoi SMS;Début incident;Fin incident;Date_DEB-mois[1-12];Date_DEB-semaine[1-52];Date_DEB-jour[1-7];DEB-J[L-D];DEB-H[9h-18h];Date_FIN-mois[1-12];Date_FIN-semaine[1-52];Date_FIN-jour[1-7];FIN-J[L-D];FIN-H[9h-18h];SMS Spécifique \n";
        $fw = fwrite($fp, $textFile);
        
        if ($fw != false) {
            $date_debut = date("Y-m-d H:i:s", strtotime("last monday", strtotime(date("Y-m-d"))));
            $date_fin = date("Y-m-d H:i:s");
            
            $result = $this->esmsApp->extractEsmsEcHebdo($date_debut, $date_fin);
            
            foreach ($result as $donnees_ticket) {
                
                $prefix_ticket = $donnees_ticket['prefix_ticket'];
                $id_ticket = $donnees_ticket['id_ticket'];
                $nature_com = utf8_decode($donnees_ticket['nature_com']);
                $specifique_com_x = $donnees_ticket['specifique_com'];
                if ($specifique_com_x == '0') {
                    $specifique_com = 'NON';
                } else {
                    $specifique_com = 'OUI';
                }
                $date_envoi_sms = $donnees_ticket['date_envoi_sms'];
                $domaine = $donnees_ticket['domaine'];
                $ref_appli = $donnees_ticket['ref_appli'];
                $nom_appli = $donnees_ticket['nom_appli'];
                $ig = $donnees_ticket['ig'];
                $date_debut_inc_t = $donnees_ticket['date_debut_inc'];
                $date_fin_inc_t = $donnees_ticket['date_fin_inc'];
                $description_x = $donnees_ticket['description'];
                $description = $this->calculDate->clean_string($description_x);
                
                if ($date_debut_inc_t == '0000-00-00 00:00:00') {
                    $date_debut_inc = '--';
                    $deb_month_inc = '--';
                    $deb_week_inc = '--';
                    $deb_day_inc = '--';
                    $debJ04 = '--';
                    $debH918 = '--';
                } else {
                    $date_debut_inc = $date_debut_inc_t;
                    $deb_month_inc = $this->calculDate->get_month_from_date($date_debut_inc_t);
                    $deb_week_inc = $this->calculDate->get_week_from_date($date_debut_inc_t);
                    $deb_day_inc = $this->calculDate->get_day_from_date($date_debut_inc_t);
                    $debJ04 = $this->calculDate->get_day_intervalle($deb_day_inc, 1, 5);
                    $debH918 = $this->calculDate->get_heure_intervalle($date_debut_inc_t, 9, 18);
                }
                if ($date_fin_inc_t == '0000-00-00 00:00:00') {
                    $date_fin_inc = '--';
                    $fin_month_inc = '--';
                    $fin_week_inc = '--';
                    $fin_day_inc = '--';
                    $finJ04 = '--';
                    $finH918 = '--';
                } else {
                    $date_fin_inc = $date_fin_inc_t;
                    $fin_month_inc = $this->calculDate->get_month_from_date($date_fin_inc_t);
                    $fin_week_inc = $this->calculDate->get_week_from_date($date_fin_inc_t);
                    $fin_day_inc = $this->calculDate->get_day_from_date($date_fin_inc_t);
                    $finJ04 = $this->calculDate->get_day_intervalle($fin_day_inc, 1, 5);
                    $finH918 = $this->calculDate->get_heure_intervalle($date_fin_inc_t, 9, 18);
                }
                
                $textFile = "$counter;$prefix_ticket;$id_ticket;$nature_com;$domaine;$ref_appli;$nom_appli;$ig;$description;$date_envoi_sms;$date_debut_inc;$date_fin_inc;$deb_month_inc;$deb_week_inc;$deb_day_inc;$debJ04;$debH918;$fin_month_inc;$fin_week_inc;$fin_day_inc;$finJ04;$finH918;$specifique_com \n";
                $counter ++;
                fwrite($fp, $textFile);
            }
            
            if ($counter > 1)
                return $extract;
            else
                echo 'fichier vide.';
        } else {
            echo 'Impossible d\'ecrire dans le fichier : ' . $extract;
        }
        fclose($fp);
        
        echo "Done!, extract hebdo_esms_ec";
    }

    /*
     * Génération esms en cours
     */
    public function esmsencoursAction()
    {
        $request = $this->getRequest();
        
        // Make sure that we are running in a console, and the user has not
        // tricked our application into running this action from a public web
        // server:
        if (! $request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }
        
        $counter = 1;
        /* Fichier joint */
        $type_extract = 'en_cours';
        $i = 0;
        $fp = true;
        $extract = "";
        $num_sem = date("W");
        
        $path = $this->config["csv"]['extract']['path'];
        
        do {
            $extract = $path . 'ExtractESMSHebdo_S' . $num_sem . '-' . $i . '.csv';
            try {
                $fp = fopen($extract, "w+");
            } catch (\Exception $e) {
                echo 'Ouverture du fichier ' . $extract . ' impossible.';
            }
            
            $i ++;
        } while ($fp == false);
        
        $textFile = "#;Prefix;No. Ticket;Nature;Domaine;REF APP;NOM APP;IG;Description;Date envoi SMS;D?but incident;Fin incident;Date_DEB-mois[1-12];Date_DEB-semaine[1-52];Date_DEB-jour[1-7];DEB-J[L-D];DEB-H[9h-18h];Date_FIN-mois[1-12];Date_FIN-semaine[1-52];Date_FIN-jour[1-7];FIN-J[L-D];FIN-H[9h-18h];SMS Sp?cifique \n";
        $fw = fwrite($fp, $textFile);
        
        if ($fw != false) {
            $date_debut = date("Y-m-d H:i:s", strtotime("last monday", strtotime(date("Y-m-d"))));
            $date_fin = date("Y-m-d H:i:s");
            $result = $this->esmsApp->esmsEnCours($date_debut, $date_fin);
            
            foreach ($result as $donnees_ticket) {
                
                $prefix_ticket = $donnees_ticket['prefix_ticket'];
                $id_ticket = $donnees_ticket['id_ticket'];
                $nature_com = utf8_decode($donnees_ticket['nature_com']);
                $specifique_com_x = $donnees_ticket['specifique_com'];
                if ($specifique_com_x == '0') {
                    $specifique_com = 'NON';
                } else {
                    $specifique_com = 'OUI';
                }
                $date_envoi_sms = $donnees_ticket['date_envoi_sms'];
                $domaine = $donnees_ticket['domaine'];
                $ref_appli = $donnees_ticket['ref_appli'];
                $nom_appli = $donnees_ticket['nom_appli'];
                $ig = $donnees_ticket['ig'];
                $date_debut_inc_t = $donnees_ticket['date_debut_inc'];
                $date_fin_inc_t = $donnees_ticket['date_fin_inc'];
                $description_x = $donnees_ticket['description'];
                $description = $this->calculDate->clean_string($description_x);
                
                if ($date_debut_inc_t == '0000-00-00 00:00:00') {
                    $date_debut_inc = '--';
                    $deb_month_inc = '--';
                    $deb_week_inc = '--';
                    $deb_day_inc = '--';
                    $debJ04 = '--';
                    $debH918 = '--';
                } else {
                    $date_debut_inc = $date_debut_inc_t;
                    $deb_month_inc = $this->calculDate->get_month_from_date($date_debut_inc_t);
                    $deb_week_inc = $this->calculDate->get_week_from_date($date_debut_inc_t);
                    $deb_day_inc = $this->calculDate->get_day_from_date($date_debut_inc_t);
                    $debJ04 = $this->calculDate->get_day_intervalle($deb_day_inc, 1, 5);
                    $debH918 = $this->calculDate->get_heure_intervalle($date_debut_inc_t, 9, 18);
                }
                if ($date_fin_inc_t == '0000-00-00 00:00:00') {
                    $date_fin_inc = '--';
                    $fin_month_inc = '--';
                    $fin_week_inc = '--';
                    $fin_day_inc = '--';
                    $finJ04 = '--';
                    $finH918 = '--';
                } else {
                    $date_fin_inc = $date_fin_inc_t;
                    $fin_month_inc = $this->calculDate->get_month_from_date($date_fin_inc_t);
                    $fin_week_inc = $this->calculDate->get_week_from_date($date_fin_inc_t);
                    $fin_day_inc = $this->calculDate->get_day_from_date($date_fin_inc_t);
                    $finJ04 = $this->calculDate->get_day_intervalle($fin_day_inc, 1, 5);
                    $finH918 = $this->calculDate->get_heure_intervalle($date_fin_inc_t, 9, 18);
                }
                
                $textFile = "$counter;$prefix_ticket;$id_ticket;$nature_com;$domaine;$ref_appli;$nom_appli;$ig;$description;$date_envoi_sms;$date_debut_inc;$date_fin_inc;$deb_month_inc;$deb_week_inc;$deb_day_inc;$debJ04;$debH918;$fin_month_inc;$fin_week_inc;$fin_day_inc;$finJ04;$finH918;$specifique_com \n";
                $counter ++;
                fwrite($fp, $textFile);
            }
            
            fclose($fp);
            // chown($extract, 'www-data');
        }
        
        echo "Done!, extract esms_encours";
    }

    public function esmshebdoAction()
    {
        $request = $this->getRequest();
        
        // Make sure that we are running in a console, and the user has not
        // tricked our application into running this action from a public web
        // server:
        if (! $request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }
        
        $counter = 1;
        
        /* Fichier joint */
        $path = $this->config["csv"]['extract']['path']; 
        
        // $path = '';
        $extract = $path . 'ExtractESMSHebdo_S' . date("W", strtotime("-1 week", strtotime(date("Y-m-d")))) . '.csv';
        try {
            $fp = fopen($extract, "w+");
        } catch (\Exception $e) {
            echo 'Ouverture du fichier impossible :' . $extract;
        }
        
        $textFile = "#;Prefix;No. Ticket;Nature;Domaine;REF APP;NOM APP;IG;Description;Date envoi SMS;Début incident;Fin incident;Date_DEB-mois[1-12];Date_DEB-semaine[1-52];Date_DEB-jour[1-7];DEB-J[L-D];DEB-H[9h-18h];Date_FIN-mois[1-12];Date_FIN-semaine[1-52];Date_FIN-jour[1-7];FIN-J[L-D];FIN-H[9h-18h];SMS Spécifique \n";
        $fw = fwrite($fp, $textFile);
        
        if ($fw != false) {
            $date_debut = date("Y-m-d H:i:s", strtotime("-1 week", strtotime("last monday", strtotime(date("Y-m-d")))));
            $date_fin = date("Y-m-d 23:59:59", strtotime("-1 week", strtotime("today sunday", strtotime(date("Y-m-d")))));
            
            $result = $this->esmsApp->extractEsmsHebdo($date_debut, $date_fin);
            
            foreach ($result as $donnees_ticket) {
                
                $prefix_ticket = $donnees_ticket['prefix_ticket'];
                $id_ticket = $donnees_ticket['id_ticket'];
                $nature_com = utf8_decode($donnees_ticket['nature_com']);
                $specifique_com_x = $donnees_ticket['specifique_com'];
                if ($specifique_com_x == '0') {
                    $specifique_com = 'NON';
                } else {
                    $specifique_com = 'OUI';
                }
                $date_envoi_sms = $donnees_ticket['date_envoi_sms'];
                $domaine = $donnees_ticket['domaine'];
                $ref_appli = $donnees_ticket['ref_appli'];
                $nom_appli = $donnees_ticket['nom_appli'];
                $ig = $donnees_ticket['ig'];
                $date_debut_inc_t = $donnees_ticket['date_debut_inc'];
                $date_fin_inc_t = $donnees_ticket['date_fin_inc'];
                $description_x = $donnees_ticket['description'];
                $description = $this->calculDate->clean_string($description_x);
                
                if ($date_debut_inc_t == '0000-00-00 00:00:00') {
                    $date_debut_inc = '--';
                    $deb_month_inc = '--';
                    $deb_week_inc = '--';
                    $deb_day_inc = '--';
                    $debJ04 = '--';
                    $debH918 = '--';
                } else {
                    $date_debut_inc = $date_debut_inc_t;
                    $deb_month_inc = $this->calculDate->get_month_from_date($date_debut_inc_t);
                    $deb_week_inc = $this->calculDate->get_week_from_date($date_debut_inc_t);
                    $deb_day_inc = $this->calculDate->get_day_from_date($date_debut_inc_t);
                    $debJ04 = $this->calculDate->get_day_intervalle($deb_day_inc, 1, 5);
                    $debH918 = $this->calculDate->get_heure_intervalle($date_debut_inc_t, 9, 18);
                }
                if ($date_fin_inc_t == '0000-00-00 00:00:00') {
                    $date_fin_inc = '--';
                    $fin_month_inc = '--';
                    $fin_week_inc = '--';
                    $fin_day_inc = '--';
                    $finJ04 = '--';
                    $finH918 = '--';
                } else {
                    $date_fin_inc = $date_fin_inc_t;
                    $fin_month_inc = $this->calculDate->get_month_from_date($date_fin_inc_t);
                    $fin_week_inc = $this->calculDate->get_week_from_date($date_fin_inc_t);
                    $fin_day_inc = $this->calculDate->get_day_from_date($date_fin_inc_t);
                    $finJ04 = $this->calculDate->get_day_intervalle($fin_day_inc, 1, 5);
                    $finH918 = $this->calculDate->get_heure_intervalle($date_fin_inc_t, 9, 18);
                }
                
                $textFile = "$counter;$prefix_ticket;$id_ticket;$nature_com;$domaine;$ref_appli;$nom_appli;$ig;$description;$date_envoi_sms;$date_debut_inc;$date_fin_inc;$deb_month_inc;$deb_week_inc;$deb_day_inc;$debJ04;$debH918;$fin_month_inc;$fin_week_inc;$fin_day_inc;$finJ04;$finH918;$specifique_com \n";
                $counter ++;
                fwrite($fp, $textFile);
            }
            
            if ($counter > 1)
                return $extract;
            else
                echo 'fichier vide.';
        } else {
            echo 'Impossible d\'ecrire dans le fichier : ' . $extract;
        }
        fclose($fp);
    }

    public function igechebdoAction()
    {
        $request = $this->getRequest();
        
        // Make sure that we are running in a console, and the user has not
        // tricked our application into running this action from a public web
        // server:
        if (! $request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }
        
        $counter = 1;
        
        /* Fichier joint */
        $path = $this->config["csv"]['extract']['path'];
        // $path = '';
        $extract = $path . 'ExtractIGHebdo_S' . date("W") . '-' . date('w') . '.csv';
        try {
            $fp = fopen($extract, "w+");
        } catch (\Exception $e) {
            echo 'Ouverture du fichier impossible : ' . $extract;
        }
        
        $textFile = "#;No. Ticket ARS;Niveau de Service;APP;Sous-Système en défaut;IG de départ;IG de fin;Durée impact;Début incident;Fin incident;Objectif DESIF;Temps avant GTR;Date_DEB-mois[1-12];Date_DEB-semaine[1-52];Date_DEB-jour[1-7];DEB-J[L-D];DEB-H[9h-18h];Date_FIN-mois[1-12];Date_FIN-semaine[1-52];Date_FIN-jour[1-7];FIN-J[L-D];FIN-H[9h-18h];GTR<12h;GTR<8h;GTR<4h;Pilote \n";
        $fw = fwrite($fp, $textFile);
        
        if ($fw != false) {
            $date_debut = date("Y-m-d H:i:s", strtotime("last monday", strtotime(date("Y-m-d"))));
            $date_fin = date("Y-m-d H:i:s");
            
            $result = $this->igApp->extractIgEcHebdo($date_debut, $date_fin);
            
            foreach ($result as $donnees_ticket) {
                
                $id_ticket = $donnees_ticket['id_ticket'];
                $typo_service = $donnees_ticket['typo_service'];
                $categorie_service = $donnees_ticket['categorie_service'];
                $debut_incident = $donnees_ticket['debut_incident'];
                $IG_ctrlDeb = $donnees_ticket['IG_ctrlDeb'];
                $IGX = $donnees_ticket['IG_debut'];
                
                if ($IG_ctrlDeb == 0) {
                    $IG = $IGX;
                } else {
                    $IG = $IG_ctrlDeb;
                }
                $cloture = $donnees_ticket['cloture'];
                $sous_systeme = $this->calculDate->clean_string($donnees_ticket['sous_systeme']);
                $duree_incident_entier = $donnees_ticket['duree_incident'];
                $date_cloture_incidentX = $donnees_ticket['date_cloture_incident'];
                $IG_finX = $donnees_ticket['IG'];
                $nom_admin = $this->calculDate->clean_string($donnees_ticket['nom_admin']);
                $date_mail_ouvert = $donnees_ticket['date_mail_ouverture'];
                if ($date_mail_ouvert == '0000-00-00 00:00:00') {
                    $date_mail_ouverture = 'Pas de comm';
                } else {
                    $date_mail_ouverture = $date_mail_ouvert;
                }
                $mail_suivi = $this->calculDate->clean_string($donnees_ticket['mail_suivi']);
                $date_mail_clos = $donnees_ticket['date_mail_cloture'];
                if ($date_mail_clos == '0000-00-00 00:00:00') {
                    $date_mail_cloture = 'Pas de comm';
                } else {
                    $date_mail_cloture = $date_mail_clos;
                }
                
                // $calcul_date = new Classes_Utile_CalculDate();
                
                $date_objectifDES = $this->calculDate->calculDateObjectif($debut_incident, $categorie_service);
                $date_actuel = date("Y-m-d H:i:s");
                
                if ($cloture == 'NON') {
                    $date_cloture_incident = 'En cours';
                    $IG_fin = '-';
                    $diffTemps = $this->calculDate->makeDate_diff($date_objectifDES, $date_actuel);
                    $GTR = $this->calculDate->date_diff($diffTemps);
                    $diffTempsDuree = $this->calculDate->makeDate_diff($debut_incident, $date_actuel);
                    $duree_incident = $this->calculDate->date_diff($diffTempsDuree);
                } else {
                    $date_cloture_incident = $date_cloture_incidentX;
                    $IG_fin = $IG_finX;
                    $diffTemps = $this->calculDate->makeDate_diff($date_objectifDES, $date_cloture_incident);
                    $GTR = $this->calculDate->date_diff($diffTemps);
                    $diffTempsDuree = $this->calculDate->makeDate_diff($debut_incident, $date_cloture_incident);
                    $duree_incident = $this->calculDate->date_diff($diffTempsDuree);
                }
                
                $deb_month_inc = $this->calculDate->get_month_from_date($debut_incident);
                $deb_week_inc = $this->calculDate->get_week_from_date($debut_incident);
                $deb_day_inc = $this->calculDate->get_day_from_date($debut_incident);
                $fin_month_inc = $this->calculDate->get_month_from_date($date_cloture_incidentX);
                $fin_week_inc = $this->calculDate->get_week_from_date($date_cloture_incidentX);
                $fin_day_inc = $this->calculDate->get_day_from_date($date_cloture_incidentX);
                $debJ04 = $this->calculDate->get_day_intervalle($deb_day_inc, 1, 5);
                $debH918 = $this->calculDate->get_heure_intervalle($debut_incident, 9, 18);
                
                if ($cloture == 'OUI') {
                    $finJ04 = $this->calculDate->get_day_intervalle($fin_day_inc, 1, 5);
                    $finH918 = $this->calculDate->get_heure_intervalle($date_cloture_incidentX, 9, 18);
                } else {
                    $finJ04 = 'En cours';
                    $finH918 = 'En cours';
                }
                $gtrM12 = $this->igDashboard->get_gtr_OK($duree_incident_entier, 12);
                $gtrM8 = $this->igDashboard->get_gtr_OK($duree_incident_entier, 8);
                $gtrM4 = $this->igDashboard->get_gtr_OK($duree_incident_entier, 4);
                
                // unset($calcul_date);
                
                $textFile = "$counter;$id_ticket;$categorie_service;$typo_service;$sous_systeme;$IG;$IG_fin;$duree_incident;$debut_incident;$date_cloture_incident;$date_objectifDES;$GTR;$deb_month_inc;$deb_week_inc;$deb_day_inc;$debJ04;$debH918;$fin_month_inc;$fin_week_inc;$fin_day_inc;$finJ04;$finH918;$gtrM12;$gtrM8;$gtrM4;$nom_admin \n";
                $counter ++;
                fwrite($fp, $textFile);
            }
            
            if ($counter > 1)
                return $extract;
            else
                echo 'fichier vide.';
        } else {
            echo 'Impossible d\'ecrire dans le fichier : ' . $extract;
        }
        fclose($fp);
    }

    public function ighebdoAction()
    {
        $request = $this->getRequest();
        
        // Make sure that we are running in a console, and the user has not
        // tricked our application into running this action from a public web
        // server:
        if (! $request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }
        
        $counter = 1;
        
        /* Fichier joint */
        $path = $this->config["csv"]['extract']['path'];
        // $path = '';
        $extract = $path . 'ExtractIGHebdo_S' . date("W", strtotime("-1 week", strtotime(date("Y-m-d")))) . '.csv';
        try {
            $fp = fopen($extract, "w+");
        } catch (\Exception $e) {
            echo 'Ouverture du fichier impossible.';
        }
        
        $textFile = "#;No. Ticket ARS;Niveau de Service;APP;Sous-Système en défaut;IG de départ;IG de fin;Durée impact;Début incident;Fin incident;Objectif DESIF;Temps avant GTR;Date_DEB-mois[1-12];Date_DEB-semaine[1-52];Date_DEB-jour[1-7];DEB-J[L-D];DEB-H[9h-18h];Date_FIN-mois[1-12];Date_FIN-semaine[1-52];Date_FIN-jour[1-7];FIN-J[L-D];FIN-H[9h-18h];GTR<12h;GTR<8h;GTR<4h;Pilote \n";
        $fw = fwrite($fp, $textFile);
        
        if ($fw != false) {
            $date_debut = date("Y-m-d H:i:s", strtotime("-1 week", strtotime("last monday", strtotime(date("Y-m-d")))));
            $date_fin = date("Y-m-d 23:59:59", strtotime("-1 week", strtotime("today sunday", strtotime(date("Y-m-d")))));
            
            $result = $this->igApp->extractIgHebdo($date_debut, $date_fin);
            
            foreach ($result as $donnees_ticket) {
                
                $id_ticket = $donnees_ticket['id_ticket'];
                $typo_service = $donnees_ticket['typo_service'];
                $categorie_service = $donnees_ticket['categorie_service'];
                $debut_incident = $donnees_ticket['debut_incident'];
                $IG_ctrlDeb = $donnees_ticket['IG_ctrlDeb'];
                $IGX = $donnees_ticket['IG_debut'];
                
                if ($IG_ctrlDeb == 0) {
                    $IG = $IGX;
                } else {
                    $IG = $IG_ctrlDeb;
                }
                $cloture = $donnees_ticket['cloture'];
                $sous_systeme = $this->calculDate->clean_string($donnees_ticket['sous_systeme']);
                $duree_incident_entier = $donnees_ticket['duree_incident'];
                $date_cloture_incidentX = $donnees_ticket['date_cloture_incident'];
                $IG_finX = $donnees_ticket['IG'];
                $nom_admin = $this->calculDate->clean_string($donnees_ticket['nom_admin']);
                $date_mail_ouvert = $donnees_ticket['date_mail_ouverture'];
                if ($date_mail_ouvert == '0000-00-00 00:00:00') {
                    $date_mail_ouverture = 'Pas de comm';
                } else {
                    $date_mail_ouverture = $date_mail_ouvert;
                }
                $mail_suivi = $this->calculDate->clean_string($donnees_ticket['mail_suivi']);
                $date_mail_clos = $donnees_ticket['date_mail_cloture'];
                if ($date_mail_clos == '0000-00-00 00:00:00') {
                    $date_mail_cloture = 'Pas de comm';
                } else {
                    $date_mail_cloture = $date_mail_clos;
                }
                
                $date_objectifDES = $this->calculDate->calculDateObjectif($debut_incident, $categorie_service);
                $date_actuel = date("Y-m-d H:i:s");
                
                if ($cloture == 'NON') {
                    $date_cloture_incident = 'En cours';
                    $IG_fin = '-';
                    $diffTemps = $this->calculDate->makeDate_diff($date_objectifDES, $date_actuel);
                    $GTR = $this->calculDate->date_diff($diffTemps);
                    $diffTempsDuree = $this->calculDate->makeDate_diff($debut_incident, $date_actuel);
                    $duree_incident = $this->calculDate->date_diff($diffTempsDuree);
                } else {
                    $date_cloture_incident = $date_cloture_incidentX;
                    $IG_fin = $IG_finX;
                    $diffTemps = $this->calculDate->makeDate_diff($date_objectifDES, $date_cloture_incident);
                    $GTR = $this->calculDate->date_diff($diffTemps);
                    $diffTempsDuree = $this->calculDate->makeDate_diff($debut_incident, $date_cloture_incident);
                    $duree_incident = $this->calculDate->date_diff($diffTempsDuree);
                }
                
                $deb_month_inc = $this->calculDate->get_month_from_date($debut_incident);
                $deb_week_inc = $this->calculDate->get_week_from_date($debut_incident);
                $deb_day_inc = $this->calculDate->get_day_from_date($debut_incident);
                $fin_month_inc = $this->calculDate->get_month_from_date($date_cloture_incidentX);
                $fin_week_inc = $this->calculDate->get_week_from_date($date_cloture_incidentX);
                $fin_day_inc = $this->calculDate->get_day_from_date($date_cloture_incidentX);
                $debJ04 = $this->calculDate->get_day_intervalle($deb_day_inc, 1, 5);
                $debH918 = $this->calculDate->get_heure_intervalle($debut_incident, 9, 18);
                
                if ($cloture == 'OUI') {
                    $finJ04 = $this->calculDate->get_day_intervalle($fin_day_inc, 1, 5);
                    $finH918 = $this->calculDate->get_heure_intervalle($date_cloture_incidentX, 9, 18);
                } else {
                    $finJ04 = 'En cours';
                    $finH918 = 'En cours';
                }
                $gtrM12 = $this->igDashboard->get_gtr_OK($duree_incident_entier, 12);
                $gtrM8 = $this->igDashboard->get_gtr_OK($duree_incident_entier, 8);
                $gtrM4 = $this->igDashboard->get_gtr_OK($duree_incident_entier, 4);
                
                unset($calcul_date);
                
                $textFile = "$counter;$id_ticket;$categorie_service;$typo_service;$sous_systeme;$IG;$IG_fin;$duree_incident;$debut_incident;$date_cloture_incident;$date_objectifDES;$GTR;$deb_month_inc;$deb_week_inc;$deb_day_inc;$debJ04;$debH918;$fin_month_inc;$fin_week_inc;$fin_day_inc;$finJ04;$finH918;$gtrM12;$gtrM8;$gtrM4;$nom_admin \n";
                $counter ++;
                fwrite($fp, $textFile);
            }
            
            if ($counter > 1)
                return $extract;
            else
                echo 'fichier vide.';
        } else {
            echo 'Impossible d\'ecrire dans le fichier : ' . $extract;
        }
        fclose($fp);
    }

    public function sixmonthsAction()
    {
        $request = $this->getRequest();
        
        // Make sure that we are running in a console, and the user has not
        // tricked our application into running this action from a public web
        // server:
        if (! $request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }
        
        $counter = 1;
        
        /* Fichier joint */
        $path = $this->config["csv"]['extract']['path'];
        // $path = '';
        $extract = $path . 'ExtractIG_6Month_S' . date("W") . '-' . date('w') . '.csv';
        try {
            $fp = fopen($extract, "w+");
        } catch (\Exception $e) {
            echo 'Ouverture du fichier impossible.';
        }
        
        $textFile = "#;No. Ticket ARS;Catégorie de Application;SI;Application;Date/Heure de création de l'incident;Date/Heure de début de l'incident;Date/Heure de création fin de l'incident;Date/Heure de fin de l'incident;Valeur d'IG de départ;Valeur d'IG en cours;Valeur d'IG de fin;Durée de l'impact;Impact de service;Clôturé;Commentaire IG;Description incident;Date 1 communication SMS;Commentaire SMS;Communication à temps;Descopé;Motif Descope;Gelé;Duration du Gêle;Nom du Pilote de l'incident;Manager d'astreinte;Messages acquittement; \n";
        $fw = fwrite($fp, $textFile);
        
        if ($fw != false) {
            $date_debut = date("Y-m-d H:i:s", strtotime("-6 month", strtotime(date("Y-m-d"))));
            $date_fin = date("Y-m-d H:i:s");
            
            $result = $this->igApp->extractSixMonths($date_debut, $date_fin);
            
            foreach ($result as $resultat) {
                
                $SI = ($resultat['SI'] == "") ? "-" : substr($resultat['SI'], 3);
                $CommentaireIG = ($resultat['CommentaireIG'] == "") ? "-" : str_replace('\\', '', $resultat['CommentaireIG']);
                $descincident = ($resultat['descincident'] == "") ? "-" : str_replace('\\', '', $resultat['descincident']);
                $sms_commentaire = ($resultat['sms_commentaire'] == "") ? "-" : str_replace('\\', '', $resultat['sms_commentaire']);
                $dsko_commentaire = ($resultat['dsko_commentaire'] == "") ? "-" : $resultat['dsko_commentaire'];
                $manager_astreinte = ($resultat['manager_astreinte'] == "") ? "-" : $resultat['manager_astreinte'];
                $commentaire = ($resultat['commentaire'] == "") ? "-" : $resultat['commentaire'];
                $ig_encours = ($resultat['cloture'] == "OUI") ? "-" : $resultat['IG'];
                $ig_fin = ($resultat['cloture'] == "OUI") ? $resultat['IG'] : "-";
                
                $duree_com_debut = "-";
                $duree_date_impact = 0;
                if ($resultat['date_sms_ouverture'] != "" && $resultat['date_sms_ouverture'] != "0000-00-00 00:00:00") {
                    $diffTempsDuree = $this->calculDate->makeDate_diff($resultat['debut_incident'], $resultat['date_sms_ouverture']);
                    $duree_com_debut = $this->calculDate->date_diff3($diffTempsDuree);
                    $diffTempsDuree = $this->calculDate->makeDate_diff($resultat['debut_incident'], $resultat['date_cloture_incident']);
                    $duree_date_impact = $this->calculDate->date_diff3($diffTempsDuree);
                }
                
                $ig_encours = ($resultat['cloture'] == "OUI") ? "" : $resultat['IG'];
                $ig_fin = ($resultat['cloture'] == "OUI") ? $resultat['IG'] : "";
                $comentig = $this->calculDate->clean_string($resultat['CommentaireIG']);
                $descincid = $this->calculDate->clean_string($resultat['descincident']);
                $sms_com = $this->calculDate->clean_string($resultat['sms_commentaire']);
                $dsko_ig = $this->calculDate->clean_string($resultat['dsko_ig']);
                $dsko_com = $this->calculDate->clean_string($resultat['dsko_commentaire']);
                $adm = $this->calculDate->clean_string($resultat['nom_admin']);
                $manager_astr = $this->calculDate->clean_string($resultat['manager_astreinte']);
                $com = $this->calculDate->clean_string($resultat['commentaire']);
                
                $text = $counter . ";" . $resultat['id_ticket'] . ";" . $resultat['categorie_service'] . ";" . $SI . ";" . $resultat['typo_service'] . ";" . $resultat['date_creation_incident'] . ";" . $resultat['debut_incident'] . ";" . $resultat['date_creation_cloture'] . ";" . $resultat['date_cloture_incident'] . ";" . $resultat['IG_debut'] . ";" . $ig_encours . ";" . $ig_fin . ";" . $duree_date_impact . ";" . $resultat['etat_service'] . ";" . $resultat['cloture'] . ";" . $comentig . ";" . $descincid . ";" . $resultat['date_sms_ouverture'] . ";" . $sms_com . ";" . $duree_com_debut . ";" . $dsko_ig . ";" . $dsko_com . ";" . $resultat['gele'] . ";" . $resultat['duree_gel'] . ";" . $adm . ";" . $manager_astr . ";" . $com . "\n";
                $counter ++;
                fwrite($fp, $text);
            }
            
            if ($counter > 1)
                return $extract;
            else
                echo 'fichier vide.';
        } else {
            echo 'Impossible d\'ecrire dans le fichier : ' . $extract;
        }
        fclose($fp);
        
        echo "Done!, extract sixmonths";
    }

    public function igtopromesAction()
    {
        $request = $this->getRequest();
        
        // Make sure that we are running in a console, and the user has not
        // tricked our application into running this action from a public web
        // server:
        if (! $request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }
        
        $counter = 1;
        
        /* Fichier joint */
        $path = $this->config["csv"]['extract']['path'];
        // $path = '';
        $extract = $path . 'ExtractESMS_J' . date("d", strtotime("-1 day", strtotime(date("Y-m-d")))) . '.csv';
        try {
            $fp = fopen($extract, "w+");
        } catch (\Exception $e) {
            echo 'Ouverture du fichier impossible.';
        }
        
        $textFile = "#;id_ticket;REF_Service;typo_service;debut_incident;date_cloture_incident;IG;Commentaire \n";
        $fw = fwrite($fp, $textFile);
        
        if ($fw != false) {
            $date_debut = date("Y-m-d", strtotime("-8 day"));
            $date_fin = date("Y-m-d", strtotime("-1 day"));
            
            $result = $this->igApp->igToPromes($date_debut, $date_fin);
            
            foreach ($result as $donnees_ticket) {
                
                $id_ticket = $donnees_ticket['id_ticket'];
                $REF_Service = $donnees_ticket['REF_Service'];
                $typo_service = $donnees_ticket['typo_service'];
                $debut_incident = $donnees_ticket['debut_incident'];
                $date_cloture_incident = $donnees_ticket['date_cloture_incident'];
                $IG = $donnees_ticket['IG'];
                $Commentaire = $this->calculDate->clean_string(utf8_decode($donnees_ticket['Commentaire']));
                
                $textFile = "$counter;$id_ticket;$REF_Service;$typo_service;$debut_incident;$date_cloture_incident;$IG;$Commentaire \n";
                $counter ++;
                fwrite($fp, $textFile);
            }
            
            if ($counter > 1) {
                $conn_id = ftp_connect($this->config["ftp"]['promes']['ip']);
                $login_result = ftp_login($conn_id, $this->config["ftp"]['promes']['username'], $this->config["ftp"]['promes']['password']);
                if (ftp_put($conn_id, '/tmp/' . 'ExtractESMS_J' . date("d", strtotime("-1 day", strtotime(date("Y-m-d")))) . '.csv', $extract, FTP_ASCII)) {
                    echo date("Y-m-d") . " : Le fichier $extract a été chargé avec succès\n";
                } else {
                    echo date("Y-m-d") . " : Il y a eu un problème lors du chargement du fichier $extract\n";
                }
                ftp_close($conn_id);
                
                return $extract;
            } else
                echo date("Y-m-d") . " : Fichier vide.\n";
        } else {
            echo date("Y-m-d") . " Impossible d\'ecrire dans le fichier : $extract";
        }
        fclose($fp);
        
        echo "Done!, extract ig_to_promes";
    }
}
