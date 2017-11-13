<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Cron\Controller;

use RuntimeException;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Cron\Model\DbTable;
use Application\Controller\BaseController;
use Zend\Console\Request as ConsoleRequest;
use Classes\Utile;

class ScriptdashboardController extends BaseController
{

    protected $db, $igApp, $esmsApp, $config, $view, $calculDate, $igDashboard, $calculEvolutionIg, $mailer;

    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        parent::dispatch($request, $response);
    }

    public function __construct($config, PhpRenderer $view, AdapterInterface $db, DbTable\IgApp $igApp, DbTable\EsmsApp $esmsApp, Utile\CalculDate $calculDate, Utile\IgDashboard $igDashboard, Utile\CalculEvolutionIg $calculEvolutionIg, Utile\Mailer $mailer)
    {
        $this->config = $config;
        $this->view = $view;
        $this->db = $db;
        $this->igApp = $igApp;
        $this->esmsApp = $esmsApp;
        $this->calculDate = $calculDate;
        $this->igDashboard = $igDashboard;
        $this->calculEvolutionIg = $calculEvolutionIg;
        $this->mailer = $mailer;
    }

    public function indexAction()
    {
        return "Successful!";
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
        
        // Récupère le chemin du backup
        $backup = $this->config['csv']['backup'];
        // Chemin de stockage fichier csv.
        $path = $this->config['csv']['extract']['path'];
        
        // // Cette fonction est dans le fichier -calcul_evolutionIg.php- elle mets à jour la table -evolution_ticket-
        // // et ça permets d'avoir l'évolution en fonction du temps
        $this->calculEvolutionIg->calculer_evolution_ticket();
        
        $counter = 1;
        $dateLastDay = date("Y-m-d H:i:s", strtotime("today -1 minute", strtotime(date("Y-m-d"))));
        $dateFirstDay = date("Y-m-d H:i:s", strtotime("last monday", strtotime(date("Y-m-d"))));
        
        /* Fichier joint */
        $num_sem = date("W", strtotime("-1 week", strtotime(date("Y-m-d"))));
        $file = 'DashboardIG_S' . $num_sem . '.csv';
        $chemin = $backup . $file;
        
        $fp = fopen("$chemin", "w");
        $textFile = "#;No. Ticket ARS;Niveau de Service;APP;Sous-Système en défaut;IG de départ;IG de fin;Durée impact;Début incident;Fin incident;Objectif DESIF;Temps avant GTR;Date_DEB-mois[1-12];Date_DEB-semaine[1-52];Date_DEB-jour[1-7];DEB-J[L-D];DEB-H[9h-18h];Date_FIN-mois[1-12];Date_FIN-semaine[1-52];Date_FIN-jour[1-7];FIN-J[L-D];FIN-H[9h-18h];GTR<12h;GTR<8h;GTR<4h;Pilote \n";
        fwrite($fp, $textFile);
        /* Fin fichier joint */
        
        $headighebdo = new ViewModel([
            'dateFirstDay' => $dateFirstDay,
            'dateLastDay' => $dateLastDay
        ]);
        $headighebdo->setTemplate('cron/scriptdashboard/ighebdo/head');
        $body = $this->view->render($headighebdo);
         
        $result = $this->igApp->ighebdo($dateFirstDay, $dateLastDay);
        
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
            $sous_systeme = $donnees_ticket['sous_systeme'];
            $duree_incident_entier = $donnees_ticket['duree_incident'];
            $date_cloture_incidentX = $donnees_ticket['date_cloture_incident'];
            $IG_finX = $donnees_ticket['IG'];
            $nom_admin = $donnees_ticket['nom_admin'];
            $date_mail_ouvert = $donnees_ticket['date_mail_ouverture'];
            if ($date_mail_ouvert == '0000-00-00 00:00:00') {
                $date_mail_ouverture = 'Pas de comm';
            } else {
                $date_mail_ouverture = $date_mail_ouvert;
            }
            $mail_suivi = $donnees_ticket['mail_suivi'];
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
            if ($diffTemps < 0) {
                $body .= "<tr bgcolor='#CD2626'>";
                $GTR = "(-)" . $GTR;
            } else {
                if (($diffTemps <= 30) and ($diffTemps >= 0)) {
                    $body .= "<tr bgcolor='#EE7600'>";
                } else {
                    $body .= "<tr>";
                }
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
            
            $textFile = "$counter;$id_ticket;$categorie_service;$typo_service;$sous_systeme;$IG;$IG_fin;$duree_incident;$debut_incident;$date_cloture_incident;$date_objectifDES;$GTR;$deb_month_inc;$deb_week_inc;$deb_day_inc;$debJ04;$debH918;$fin_month_inc;$fin_week_inc;$fin_day_inc;$finJ04;$finH918;$gtrM12;$gtrM8;$gtrM4;$nom_admin \n";
            fwrite($fp, $textFile);
            
            $bodyighebdo = new ViewModel([
                'counter' => $counter,
                'id_ticket' => $id_ticket,
                'categorie_service' => $categorie_service,
                'typo_service' => $typo_service,
                'sous_systeme' => $sous_systeme,
                'IG'=>$IG,
                'IG_fin'=>$IG_fin,
                'duree_incident'=>$duree_incident,
                'debut_incident'=>$debut_incident,
                'cloture'=>$cloture,
                'date_cloture_incident'=>$date_cloture_incident,
                'date_objectifDES'=>$date_objectifDES,
                'GTR'=>$GTR,
                'deb_month_inc'=>$deb_month_inc,
                'deb_week_inc'=>$deb_week_inc,
                'deb_day_inc'=>$deb_day_inc,
                'debJ04'=>$debJ04,
                'debH918'=>$debH918,
                'fin_month_inc'=>$fin_month_inc,
                'fin_week_inc'=>$fin_week_inc,
                'fin_day_inc'=>$fin_day_inc,
                'finJ04'=>$finJ04,
                'finH918'=>$finH918,
                'gtrM12'=>$gtrM12,
                'gtrM8'=>$gtrM8,
                'gtrM4'=>$gtrM4,
                'nom_admin'=>$nom_admin
            ]);
            $bodyighebdo->setTemplate('cron/scriptdashboard/ighebdo/body');
            $body .= $this->view->render($bodyighebdo);

            $counter ++;
        }
        
        fclose($fp);
        
        $body .= "</table>";
        $body .= "<br>";
        $body .= "<table>";
        $body .= "<tr><td bgcolor='#CD2626'><font color='#CD2626' size='1'>ok</font></td>";
        $body .= "<td><font face='verdana' size='1'>GTR dépassée</font></td>";
        $body .= "</tr>";
        $body .= "<tr><td bgcolor='#EE7600'><font color='#EE7600' size='1'>ok</font></td>";
        $body .= "<td><font face='verdana' size='1'>GTR dans 30 min</font></td>";
        $body .= "</tr>";
        $body .= "</table>";
        
        // ***********************************************Fin partie dashboard GTR tickets en cours**********************************//
        // *****************************************Partie dashboard moyenne de GTR tickets du mois*******************************************//
        // / Calcul du nombre total de tickets par catégorie
        $numTicketsPlatinium = $this->igDashboard->getNumCategorie('Platinium', $dateFirstDay, $dateLastDay);
        $numTicketsOr = $this->igDashboard->getNumCategorie('Or', $dateFirstDay, $dateLastDay);
        $numTicketsArgent = $this->igDashboard->getNumCategorie('Argent', $dateFirstDay, $dateLastDay);
        $numTicketsBestEffort = $this->igDashboard->getNumCategorie('Best Effort', $dateFirstDay, $dateLastDay);
        $numTicketsTotal = $numTicketsPlatinium + $numTicketsOr + $numTicketsArgent + $numTicketsBestEffort;
        // /Fin calcul nombre total de tickets
        // /Assigner les valeurs de gtr par catégories
        $gtrP = 4;
        $gtrO = 4;
        $gtrA = 8;
        $gtrBF = 24;
        // /Calcul du nombre de tickets ok par catégorie
        $OkPlatinium = $this->igDashboard->getNumGTRok('Platinium', $gtrP, $dateFirstDay, $dateLastDay);
        $OkOr = $this->igDashboard->getNumGTRok('Or', $gtrO, $dateFirstDay, $dateLastDay);
        $OkArgent = $this->igDashboard->getNumGTRok('Argent', $gtrA, $dateFirstDay, $dateLastDay);
        $OkBestEffort = $this->igDashboard->getNumGTRok('Best Effort', $gtrBF, $dateFirstDay, $dateLastDay);
        $OkTotal = $OkPlatinium + $OkOr + $OkArgent + $OkBestEffort;
        // / Calcul du nombre de tickets nok par catégorie
        $NokPlatinium = $this->igDashboard->getNumGTRnok('Platinium', $gtrP, $dateFirstDay, $dateLastDay);
        $NokOr = $this->igDashboard->getNumGTRnok('Or', $gtrO, $dateFirstDay, $dateLastDay);
        $NokArgent = $this->igDashboard->getNumGTRnok('Argent', $gtrA, $dateFirstDay, $dateLastDay);
        $NokBestEffort = $this->igDashboard->getNumGTRnok('Best Effort', $gtrBF, $dateFirstDay, $dateLastDay);
        $NokTotal = $NokPlatinium + $NokOr + $NokArgent + $NokBestEffort;
        // /
        // / Calcul de la moyenne gtr par catégorie, le résultat est -integer-
        $xPlatinium = $this->igDashboard->getMoyenneGTR('Platinium', $gtrP, $dateFirstDay, $dateLastDay);
        $xOr = $this->igDashboard->getMoyenneGTR('Or', $gtrO, $dateFirstDay, $dateLastDay);
        $xArgent = $this->igDashboard->getMoyenneGTR('Argent', $gtrA, $dateFirstDay, $dateLastDay);
        $xBestEffort = $this->igDashboard->getMoyenneGTR('Best Effort', $gtrBF, $dateFirstDay, $dateLastDay);
        // / Calcul de la moyenne gtr par catégorie, el résultat est en format date
        $moyenneP = $this->igDashboard->getMoyenneGTRDate('Platinium', $dateFirstDay, $dateLastDay);
        $moyenneO = $this->igDashboard->getMoyenneGTRDate('Or', $dateFirstDay, $dateLastDay);
        $moyenneA = $this->igDashboard->getMoyenneGTRDate('Argent', $dateFirstDay, $dateLastDay);
        $moyenneBF = $this->igDashboard->getMoyenneGTRDate('Best Effort', $dateFirstDay, $dateLastDay);
        // / Calcul du % de tickets OK
        $pourcentageOkP = $this->igDashboard->makePercent($OkPlatinium, $numTicketsPlatinium);
        $pourcentageOkO = $this->igDashboard->makePercent($OkOr, $numTicketsOr);
        $pourcentageOkA = $this->igDashboard->makePercent($OkArgent, $numTicketsArgent);
        $pourcentageOkBF = $this->igDashboard->makePercent($OkBestEffort, $numTicketsBestEffort);
        // //Fin calcul %
        // /
        $body .= "<br><br>";
        $body .= "<div style='width:750;background:#EEEEE0;'>";
        $body .= "<table width='600' class='dashGTR' cellpadding='1' cellspacing='2'>";
        $body .= "<tr><td colspan='7'><font face='verdana' size='1' color='#000080'><b>PERIODE:   </b>  $dateFirstDay <b>-----</b> $dateLastDay</font></td></tr>";
        $body .= "<tr><td colspan='7'></td></tr>";
        $body .= "<tr><td colspan='7'></td></tr>";
        $body .= "<tr>
	<td></td>
	<td class='title' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>Moyenne Hebdomadaire</b></font></td>
	<td class='title' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>Niveau Service</b></font></td>
	<td class='title' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>GTR</b></font></td>
	<td class='title' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>% Tickets OK</b></font></td>
	<td class='title' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>Tickets OK</b></font></td>
	<td class='title' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>Tickets NOK</b></font></td>
	</tr>";
        $body .= "<tr>";
        if ($pourcentageOkP >= 80) {
            $body .= "<td class='feu' bgcolor='#66CD00'><font color='#66CD00' size='1'>ok</font></td>";
        }
        if (($pourcentageOkP > 70) and ($pourcentageOkP < 80)) {
            $body .= "<td class='feu' bgcolor='#EE7600'><font color='#EE7600' size='1'>ok</font></td>";
        }
        if ($pourcentageOkP <= 70) {
            $body .= "<td class='feu' bgcolor='#CD2626'><font color='#CD2626' size='1'>ok</font></td>";
        }
        $body .= "
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$moyenneP</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>Platinium</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$gtrP hrs</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$pourcentageOkP %</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$OkPlatinium</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$NokPlatinium</font></td>
        </tr>";
        $body .= "<tr>";
        if ($pourcentageOkO >= 80) {
            $body .= "<td class='feu' bgcolor='#66CD00'><font color='#66CD00' size='1'>ok</font></td>";
        }
        if (($pourcentageOkO > 70) and ($pourcentageOkO < 80)) {
            $body .= "<td class='feu' bgcolor='#EE7600'><font color='#EE7600' size='1'>ok</font></td>";
        }
        if ($pourcentageOkO <= 70) {
            $body .= "<td class='feu' bgcolor='#CD2626'><font color='#CD2626' size='1'>ok</font></td>";
        }
        $body .= "
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$moyenneO</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>Or</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$gtrO hrs</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$pourcentageOkO %</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$OkOr</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$NokOr</font></td>
        </tr>";
        $body .= "<tr>";
        if ($pourcentageOkA >= 80) {
            $body .= "<td class='feu' bgcolor='#66CD00'><font color='#66CD00' size='1'>ok</font></td>";
        }
        if (($pourcentageOkA > 70) and ($pourcentageOkA < 80)) {
            $body .= "<td class='feu' bgcolor='#EE7600'><font color='#EE7600' size='1'>ok</font></td>";
        }
        if ($pourcentageOkA <= 70) {
            $body .= "<td class='feu' bgcolor='#CD2626'><font color='#CD2626' size='1'>ok</font></td>";
        }
        $body .= "
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$moyenneA</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>Argent</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$gtrA hrs</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$pourcentageOkA %</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$OkArgent</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$NokArgent</font></td>
        </tr>";
        $body .= "<tr>";
        if ($pourcentageOkBF >= 80) {
            $body .= "<td class='feu' bgcolor='#66CD00'><font color='#66CD00' size='1'>ok</font></td>";
        }
        if (($pourcentageOkBF > 70) and ($pourcentageOkBF < 80)) {
            $body .= "<td class='feu' bgcolor='#EE7600'><font color='#EE7600' size='1'>ok</font></td>";
        }
        if ($pourcentageOkBF <= 70) {
            $body .= "<td class='feu' bgcolor='#CD2626'><font color='#CD2626' size='1'>ok</font></td>";
        }
        $body .= "
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$moyenneBF</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>Best Effort</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$gtrBF hrs</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$pourcentageOkBF %</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$OkBestEffort</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$NokBestEffort</font></td>
        </tr>";
        $body .= "<tr>
        <td colspan='4'></td>
        <td><font face='verdana' size='1' color='#000080'></font></td>
        <td class='feu' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>$OkTotal</b></font></td>
        <td class='feu' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>$NokTotal</b></font></td>
        </tr>";
        $body .= "<tr><td colspan='7'></td></tr>";
        $body .= "<tr><td colspan='7'></td></tr>";
        $body .= "</table>";
        $body .= "</div>";
        // ***********************************************Fin partie dashboard moyenne GTR tickets du mois**********************************//
        /**
         * *****
         * Enregistrement du pourcentage acumulé dans le mois precedent.
         * *****
         */
        $annee = date("Y", strtotime("-1 week", strtotime(date("Y-m-d"))));
        $num_mois = date("n", strtotime("-1 week", strtotime(date("Y-m-d"))));
        $num_semaine = date("W", strtotime("-1 week", strtotime(date("Y-m-d"))));
        if ($num_mois == 1) {
            $mois = 'janvier';
        }
        if ($num_mois == 2) {
            $mois = 'fevrier';
        }
        if ($num_mois == 3) {
            $mois = 'mars';
        }
        if ($num_mois == 4) {
            $mois = 'avril';
        }
        if ($num_mois == 5) {
            $mois = 'mai';
        }
        if ($num_mois == 6) {
            $mois = 'juin';
        }
        if ($num_mois == 7) {
            $mois = 'juillet';
        }
        if ($num_mois == 8) {
            $mois = utf8_encode('août');
        }
        if ($num_mois == 9) {
            $mois = 'septembre';
        }
        if ($num_mois == 10) {
            $mois = 'octobre';
        }
        if ($num_mois == 11) {
            $mois = 'novembre';
        }
        if ($num_mois == 12) {
            $mois = 'decembre';
        }
        
        $query_P = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','PLA','$pourcentageOkP','$OkPlatinium','$NokPlatinium')";
        $this->igApp->doQuery($query_P);
        $query_O = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','OR','$pourcentageOkO','$OkOr','$NokOr')";
        $this->igApp->doQuery($query_O);
        $query_A = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','ARG','$pourcentageOkA','$OkArgent','$NokArgent')";
        $this->igApp->doQuery($query_A);
        $query_BF = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','BEF','$pourcentageOkBF','$OkBestEffort','$NokBestEffort')";
        $this->igApp->doQuery($query_BF);
        
        /**
         * **
         * Fin enregistrement
         * **
         */
        
        $fichier = $path . "ig_hebdo_si.csv";
        // ***********************************************Mise a disposition du fichier dans le répertoire /varsoft/igsi/dataout/**********************************//
        $command = "cp $chemin $fichier";
        exec($command);
        // ***********************************************Fin mise a disposition du fichier hebdo**********************************//
        
        /**
         * ****
         * Envoi de mail hebdo
         * ****
         */
        
        $host = $this->config['mailIgHebdo']['host'];
        
        $username = $this->config['mailIgHebdo']['username']; // SMTP username
        $password = $this->config['mailIgHebdo']['password']; // SMTP password
        $port = $this->config['mailIgHebdo']['port'];
        
        $from = $this->config['mailIgHebdo']['from'];
        $sender = $this->config['mailIgHebdo']['sender'];
        $fromName = $this->config['mailIgHebdo']['fromName'];
        $to = $this->config['mailIgHebdo']['to'];
        $cc = $this->config['mailIgHebdo']['cc'];
        
        $attachs[0] = [
            $chemin,
            $file
        ]; // fichier attaché
        
        $sujet = "Dashboard IG Tools SI $num_semaine $mois $annee";
        
        $this->mailer->sendPHPMailer($body, $sujet, $host, $username, $password, $port, $from, $sender, $fromName, $to, $cc, $attachs);
        
        echo "Done!, ighebdo";
    }

    public function igmensuelAction()
    {
        $request = $this->getRequest();
        
        // Make sure that we are running in a console, and the user has not
        // tricked our application into running this action from a public web
        // server:
        if (! $request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }
        
        // Récupère le chemin du backup
        $backup = $this->config['csv']['backup'];
        // Chemin de stockage fichier csv.
        $path = $this->config['csv']['extract']['path'];
        
        // // Cette fonction est dans le fichier -calcul_evolutionIg.php- elle mets à jour la table -evolution_ticket-
        // // et ça permets d'avoir l'évolution en fonction du temps
        $this->calculEvolutionIg->calculer_evolution_ticket();
        
        $counter = 1;
        $dateLastDay = date("Y-m-d H:i:s", strtotime("-1 minute", strtotime(date("Y-m-01"))));
        $dateFirstDay = date("Y-m-01 H:i:s", strtotime("-1 month", strtotime(date("Y-m-d"))));
        
        /* Fichier joint */
        $year = date("Y", strtotime("-1 month", strtotime(date("Y-m-d"))));
        $n_mois = date("n", strtotime("-1 month", strtotime(date("Y-m-d"))));
        
        $file = 'DashboardIG_' . $n_mois . '_' . $year . '.csv';
        $chemin = $backup . $file;
        
        $fp = fopen("$chemin", "w");
        $textFile = "#;No. Ticket ARS;Niveau de Service;APP;Sous-Système en défaut;IG de départ;IG de fin;Durée impact;Début incident;Fin incident;Objectif DESIF;Temps avant GTR;Date_DEB-mois[1-12];Date_DEB-semaine[1-52];Date_DEB-jour[1-7];DEB-J[L-D];DEB-H[9h-18h];Date_FIN-mois[1-12];Date_FIN-semaine[1-52];Date_FIN-jour[1-7];FIN-J[L-D];FIN-H[9h-18h];GTR<12h;GTR<8h;GTR<4h;Pilote \n";
        fwrite($fp, $textFile);
        /* Fin fichier joint */
        
        $body = "<font face='verdana' size='3'><b>Dashboard IG du <font color='#000080'>$dateFirstDay</font>  au  <font color='#000080'>$dateLastDay </font></b></font>";
        $body .= "<table class=\"migStyle\" border='1' width='97%' bordercolor='gray' cellpadding='1' cellspacing='0'>
	  <tr><th width='auto'><b>#</b></th>
	  <th width='auto'><font face='verdana' size='1'><b>No. Ticket</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Niveau Service</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>APP</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Sous-Système en défaut</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>IG de départ</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>IG de fin</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Durée impact</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Début de l'incident</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date de clôture</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Objectif DESIF</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Temps avant GTR</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_DEB-mois[1-12]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_DEB-semaine[1-52]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_DEB-jour[1-7]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>DEB-J[L-D]</b></font></th>	  
	  <th width='auto'><font face='verdana' size='1'><b>DEB-H[9h-18h]</b></font></th>	  
	  <th width='auto'><font face='verdana' size='1'><b>Date_FIN-mois[1-12]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_FIN-semaine[1-52]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_FIN-jour[1-7]</b></font></th>	  
	  <th width='auto'><font face='verdana' size='1'><b>FIN-J[L-D]</b></font></th>	  
	  <th width='auto'><font face='verdana' size='1'><b>FIN-H[9h-18h]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>GTR<12h</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>GTR<8h</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>GTR<4h</b></font></th>	  
	  <th width='auto'><font face='verdana' size='1'><b>Pilote incident</b></font></th></tr>";
        
        $result = $this->igApp->igmensuel($dateFirstDay, $dateLastDay);
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
            $sous_systeme = $donnees_ticket['sous_systeme'];
            $duree_incident_entier = $donnees_ticket['duree_incident'];
            $date_cloture_incidentX = $donnees_ticket['date_cloture_incident'];
            $IG_finX = $donnees_ticket['IG'];
            $nom_admin = $donnees_ticket['nom_admin'];
            $date_mail_ouvert = $donnees_ticket['date_mail_ouverture'];
            if ($date_mail_ouvert == '0000-00-00 00:00:00') {
                $date_mail_ouverture = 'Pas de comm';
            } else {
                $date_mail_ouverture = $date_mail_ouvert;
            }
            $mail_suivi = $donnees_ticket['mail_suivi'];
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
            if ($diffTemps < 0) {
                $body .= "<tr bgcolor='#CD2626'>";
                $GTR = "(-)" . $GTR;
            } else {
                if (($diffTemps <= 30) and ($diffTemps >= 0)) {
                    $body .= "<tr bgcolor='#EE7600'>";
                } else {
                    $body .= "<tr>";
                }
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
            
            $textFile = "$counter;$id_ticket;$categorie_service;$typo_service;$sous_systeme;$IG;$IG_fin;$duree_incident;$debut_incident;$date_cloture_incident;$date_objectifDES;$GTR;$deb_month_inc;$deb_week_inc;$deb_day_inc;$debJ04;$debH918;$fin_month_inc;$fin_week_inc;$fin_day_inc;$finJ04;$finH918;$gtrM12;$gtrM8;$gtrM4;$nom_admin \n";
            fwrite($fp, $textFile);
            $body .= "<td><font face='verdana' size='1'>$counter</font></td>
            <td><font face='verdana' size='1'>$id_ticket</font></td>
            <td><font face='verdana' size='1'>$categorie_service</font></td>
            <td><font face='verdana' size='1'>$typo_service</font></td>
            <td><font face='verdana' size='1'><font face='verdana' size='1'>$sous_systeme</font></td>
            <td><font face='verdana' size='1'>$IG</font></td>
            <td><font face='verdana' size='1'>$IG_fin</font></td>
            <td><font face='verdana' size='1'>$duree_incident</font></td>
            <td><font face='verdana' size='1'>$debut_incident</font></td>";
            if ($cloture == 'OUI') {
                $body .= "<td bgcolor='#A2B5CD'><font face='verdana' size='1'>$date_cloture_incident</font></td>";
            } else {
                $body .= "<td><font face='verdana' size='1'>$date_cloture_incident</font></td>";
            }
            $body .= "
            <td><font face='verdana' size='1'>$date_objectifDES</font></td>
            <td><font face='verdana' size='1'>$GTR</font></td>
            <td><font face='verdana' size='1'>$deb_month_inc</font></td>
            <td><font face='verdana' size='1'>$deb_week_inc</font></td>
            <td><font face='verdana' size='1'>$deb_day_inc</font></td>
            <td><font face='verdana' size='1'>$debJ04</font></td>
            <td><font face='verdana' size='1'>$debH918</font></td>
            <td><font face='verdana' size='1'>$fin_month_inc</font></td>
            <td><font face='verdana' size='1'>$fin_week_inc</font></td>
            <td><font face='verdana' size='1'>$fin_day_inc</font></td>
            <td><font face='verdana' size='1'>$finJ04</font></td>
            <td><font face='verdana' size='1'>$finH918</font></td>
            <td><font face='verdana' size='1'>$gtrM12</font></td>
            <td><font face='verdana' size='1'>$gtrM8</font></td>
            <td><font face='verdana' size='1'>$gtrM4</font></td>
            <td><font face='verdana' size='1'>$nom_admin</font></td>";
            $body .= "</tr>";
            $counter ++;
        }
        
        fclose($fp);
        
        $body .= "</table>";
        $body .= "<br>";
        $body .= "<table>";
        $body .= "<tr><td bgcolor='#CD2626'><font color='#CD2626' size='1'>ok</font></td>";
        $body .= "<td><font face='verdana' size='1'>GTR dépassée</font></td>";
        $body .= "</tr>";
        $body .= "<tr><td bgcolor='#EE7600'><font color='#EE7600' size='1'>ok</font></td>";
        $body .= "<td><font face='verdana' size='1'>GTR dans 30 min</font></td>";
        $body .= "</tr>";
        $body .= "</table>";
        
        // ***********************************************Fin partie dashboard GTR tickets en cours**********************************//
        // *****************************************Partie dashboard moyenne de GTR tickets du mois*******************************************//
        // / Calcul du nombre total de tickets par catégorie
        $numTicketsPlatinium = $this->igDashboard->getNumCategorie('Platinium', $dateFirstDay, $dateLastDay);
        $numTicketsOr = $this->igDashboard->getNumCategorie('Or', $dateFirstDay, $dateLastDay);
        $numTicketsArgent = $this->igDashboard->getNumCategorie('Argent', $dateFirstDay, $dateLastDay);
        $numTicketsBestEffort = $this->igDashboard->getNumCategorie('Best Effort', $dateFirstDay, $dateLastDay);
        $numTicketsTotal = $numTicketsPlatinium + $numTicketsOr + $numTicketsArgent + $numTicketsBestEffort;
        // /Fin calcul nombre total de tickets
        // /Assigner les valeurs de gtr par catégories
        $gtrP = 4;
        $gtrO = 4;
        $gtrA = 8;
        $gtrBF = 24;
        // /Calcul du nombre de tickets ok par catégorie
        $OkPlatinium = $this->igDashboard->getNumGTRok('Platinium', $gtrP, $dateFirstDay, $dateLastDay);
        $OkOr = $this->igDashboard->getNumGTRok('Or', $gtrO, $dateFirstDay, $dateLastDay);
        $OkArgent = $this->igDashboard->getNumGTRok('Argent', $gtrA, $dateFirstDay, $dateLastDay);
        $OkBestEffort = $this->igDashboard->getNumGTRok('Best Effort', $gtrBF, $dateFirstDay, $dateLastDay);
        $OkTotal = $OkPlatinium + $OkOr + $OkArgent + $OkBestEffort;
        // / Calcul du nombre de tickets nok par catégorie
        $NokPlatinium = $this->igDashboard->getNumGTRnok('Platinium', $gtrP, $dateFirstDay, $dateLastDay);
        $NokOr = $this->igDashboard->getNumGTRnok('Or', $gtrO, $dateFirstDay, $dateLastDay);
        $NokArgent = $this->igDashboard->getNumGTRnok('Argent', $gtrA, $dateFirstDay, $dateLastDay);
        $NokBestEffort = $this->igDashboard->getNumGTRnok('Best Effort', $gtrBF, $dateFirstDay, $dateLastDay);
        $NokTotal = $NokPlatinium + $NokOr + $NokArgent + $NokBestEffort;
        // /
        // / Calcul de la moyenne gtr par catégorie, le résultat est -integer-
        $xPlatinium = $this->igDashboard->getMoyenneGTR('Platinium', $gtrP, $dateFirstDay, $dateLastDay);
        $xOr = $this->igDashboard->getMoyenneGTR('Or', $gtrO, $dateFirstDay, $dateLastDay);
        $xArgent = $this->igDashboard->getMoyenneGTR('Argent', $gtrA, $dateFirstDay, $dateLastDay);
        $xBestEffort = $this->igDashboard->getMoyenneGTR('Best Effort', $gtrBF, $dateFirstDay, $dateLastDay);
        // / Calcul de la moyenne gtr par catégorie, el résultat est en format date
        $moyenneP = $this->igDashboard->getMoyenneGTRDate('Platinium', $dateFirstDay, $dateLastDay);
        $moyenneO = $this->igDashboard->getMoyenneGTRDate('Or', $dateFirstDay, $dateLastDay);
        $moyenneA = $this->igDashboard->getMoyenneGTRDate('Argent', $dateFirstDay, $dateLastDay);
        $moyenneBF = $this->igDashboard->getMoyenneGTRDate('Best Effort', $dateFirstDay, $dateLastDay);
        // / Calcul du % de tickets OK
        $pourcentageOkP = $this->igDashboard->makePercent($OkPlatinium, $numTicketsPlatinium);
        $pourcentageOkO = $this->igDashboard->makePercent($OkOr, $numTicketsOr);
        $pourcentageOkA = $this->igDashboard->makePercent($OkArgent, $numTicketsArgent);
        $pourcentageOkBF = $this->igDashboard->makePercent($OkBestEffort, $numTicketsBestEffort);
        // //Fin calcul %
        // /
        $body .= "<br><br>";
        $body .= "<div style='width:750;background:#EEEEE0;'>";
        $body .= "<table width='600' class='dashGTR' cellpadding='1' cellspacing='2'>";
        $body .= "<tr><td colspan='7'><font face='verdana' size='1' color='#000080'><b>PERIODE:   </b>  $dateFirstDay <b>-----</b> $dateLastDay</font></td></tr>";
        $body .= "<tr><td colspan='7'></td></tr>";
        $body .= "<tr><td colspan='7'></td></tr>";
        $body .= "<tr>
	<td></td>
	<td class='title' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>Moyenne Hebdomadaire</b></font></td>
	<td class='title' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>Niveau Service</b></font></td>
	<td class='title' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>GTR</b></font></td>
	<td class='title' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>% Tickets OK</b></font></td>
	<td class='title' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>Tickets OK</b></font></td>
	<td class='title' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>Tickets NOK</b></font></td>
	</tr>";
        $body .= "<tr>";
        if ($pourcentageOkP >= 80) {
            $body .= "<td class='feu' bgcolor='#66CD00'><font color='#66CD00' size='1'>ok</font></td>";
        }
        if (($pourcentageOkP > 70) and ($pourcentageOkP < 80)) {
            $body .= "<td class='feu' bgcolor='#EE7600'><font color='#EE7600' size='1'>ok</font></td>";
        }
        if ($pourcentageOkP <= 70) {
            $body .= "<td class='feu' bgcolor='#CD2626'><font color='#CD2626' size='1'>ok</font></td>";
        }
        $body .= "
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$moyenneP</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>Platinium</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$gtrP hrs</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$pourcentageOkP %</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$OkPlatinium</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$NokPlatinium</font></td>
        </tr>";
        $body .= "<tr>";
        if ($pourcentageOkO >= 80) {
            $body .= "<td class='feu' bgcolor='#66CD00'><font color='#66CD00' size='1'>ok</font></td>";
        }
        if (($pourcentageOkO > 70) and ($pourcentageOkO < 80)) {
            $body .= "<td class='feu' bgcolor='#EE7600'><font color='#EE7600' size='1'>ok</font></td>";
        }
        if ($pourcentageOkO <= 70) {
            $body .= "<td class='feu' bgcolor='#CD2626'><font color='#CD2626' size='1'>ok</font></td>";
        }
        $body .= "
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$moyenneO</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>Or</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$gtrO hrs</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$pourcentageOkO %</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$OkOr</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$NokOr</font></td>
        </tr>";
        $body .= "<tr>";
        if ($pourcentageOkA >= 80) {
            $body .= "<td class='feu' bgcolor='#66CD00'><font color='#66CD00' size='1'>ok</font></td>";
        }
        if (($pourcentageOkA > 70) and ($pourcentageOkA < 80)) {
            $body .= "<td class='feu' bgcolor='#EE7600'><font color='#EE7600' size='1'>ok</font></td>";
        }
        if ($pourcentageOkA <= 70) {
            $body .= "<td class='feu' bgcolor='#CD2626'><font color='#CD2626' size='1'>ok</font></td>";
        }
        $body .= "
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$moyenneA</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>Argent</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$gtrA hrs</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$pourcentageOkA %</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$OkArgent</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$NokArgent</font></td>
        </tr>";
        $body .= "<tr>";
        if ($pourcentageOkBF >= 80) {
            $body .= "<td class='feu' bgcolor='#66CD00'><font color='#66CD00' size='1'>ok</font></td>";
        }
        if (($pourcentageOkBF > 70) and ($pourcentageOkBF < 80)) {
            $body .= "<td class='feu' bgcolor='#EE7600'><font color='#EE7600' size='1'>ok</font></td>";
        }
        if ($pourcentageOkBF <= 70) {
            $body .= "<td class='feu' bgcolor='#CD2626'><font color='#CD2626' size='1'>ok</font></td>";
        }
        $body .= "
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$moyenneBF</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>Best Effort</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$gtrBF hrs</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$pourcentageOkBF %</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$OkBestEffort</font></td>
        <td class='feu' bgcolor='#FFFFFF'><font face='verdana' size='1' color='#000080'>$NokBestEffort</font></td>
        </tr>";
        $body .= "<tr>
        <td colspan='4'></td>
        <td><font face='verdana' size='1' color='#000080'></font></td>
        <td class='feu' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>$OkTotal</b></font></td>
        <td class='feu' bgcolor='#CDC9C9'><font face='verdana' size='1' color='#000080'><b>$NokTotal</b></font></td>
        </tr>";
        $body .= "<tr><td colspan='7'></td></tr>";
        $body .= "<tr><td colspan='7'></td></tr>";
        $body .= "</table>";
        $body .= "</div>";
        // ***********************************************Fin partie dashboard moyenne GTR tickets du mois**********************************//
        /**
         * *****
         * Enregistrement du pourcentage acumulé dans le mois precedent.
         * *****
         */
        $annee = date("Y", strtotime("-1 week", strtotime(date("Y-m-d"))));
        $num_mois = date("n", strtotime("-1 week", strtotime(date("Y-m-d"))));
        $num_semaine = date("W", strtotime("-1 week", strtotime(date("Y-m-d"))));
        if ($num_mois == 1) {
            $mois = 'janvier';
        }
        if ($num_mois == 2) {
            $mois = 'fevrier';
        }
        if ($num_mois == 3) {
            $mois = 'mars';
        }
        if ($num_mois == 4) {
            $mois = 'avril';
        }
        if ($num_mois == 5) {
            $mois = 'mai';
        }
        if ($num_mois == 6) {
            $mois = 'juin';
        }
        if ($num_mois == 7) {
            $mois = 'juillet';
        }
        if ($num_mois == 8) {
            $mois = utf8_encode('août');
        }
        if ($num_mois == 9) {
            $mois = 'septembre';
        }
        if ($num_mois == 10) {
            $mois = 'octobre';
        }
        if ($num_mois == 11) {
            $mois = 'novembre';
        }
        if ($num_mois == 12) {
            $mois = 'decembre';
        }
        
        $query_P = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','PLA','$pourcentageOkP','$OkPlatinium','$NokPlatinium')";
        $this->igApp->doQuery($query_P);
        $query_O = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','OR','$pourcentageOkO','$OkOr','$NokOr')";
        $this->igApp->doQuery($query_O);
        $query_A = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','ARG','$pourcentageOkA','$OkArgent','$NokArgent')";
        $this->igApp->doQuery($query_A);
        $query_BF = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','BEF','$pourcentageOkBF','$OkBestEffort','$NokBestEffort')";
        $this->igApp->doQuery($query_BF);
        /**
         * **
         * Fin enregistrement
         * **
         */
        
        $fichier = $path . "ig_hebdo_si.csv";
        // ***********************************************Mise a disposition du fichier dans le répertoire /varsoft/igsi/dataout/**********************************//
        $command = "cp $chemin $fichier";
        exec($command);
        // ***********************************************Fin mise a disposition du fichier hebdo**********************************//
        
        /**
         * ****
         * Envoi de mail hebdo
         * ****
         */
        
        $host = $this->config['igmensuel']['host'];
        
        $username = $this->config['igmensuel']['username']; // SMTP username
        $password = $this->config['igmensuel']['password']; // SMTP password
        $port = $this->config['igmensuel']['port'];
        
        $from = $this->config['igmensuel']['from'];
        $sender = $this->config['igmensuel']['sender'];
        $fromName = $this->config['igmensuel']['fromName'];
        $to = $this->config['igmensuel']['to'];
        $cc = $this->config['igmensuel']['cc'];
        
        $attachs[0] = [
            $chemin,
            $file
        ]; // fichier attaché
        
        $sujet = "Dashboard IG Tools SI $mois $annee";
        
        $this->mailer->sendPHPMailer($body, $sujet, $host, $username, $password, $port, $from, $sender, $fromName, $to, $cc, $attachs);
        
        echo "Done!, igmensuel";
    }

    public function esmsigsihebdoAction()
    {
        $request = $this->getRequest();
        
        // Make sure that we are running in a console, and the user has not
        // tricked our application into running this action from a public web
        // server:
        if (! $request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }
        
        // Récupèrer le chemin du backup
        $backup = $this->config['csv']['backup'];
        // Récuperer le chemin dataout
        $dataout = $this->config['csv']['dataout'];
        // Récuperer chemin de stockage fichier csv.
        $path = $this->config['csv']['extract']['path'];
        
        // // Cette fonction est dans le fichier -calcul_evolutionIg.php- elle mets à jour la table -evolution_ticket-
        // // et ça permets d'avoir l'évolution en fonction du temps
        $this->calculEvolutionIg->calculer_evolution_ticket();
        
        $counter = 1;
        $dateLastDay = date("Y-m-d H:i:s", strtotime("today -1 minute", strtotime(date("Y-m-d"))));
        $dateFirstDay = date("Y-m-d H:i:s", strtotime("last monday", strtotime(date("Y-m-d"))));
        
        /* Fichier joint */
        $num_sem = date("W", strtotime("-1 week", strtotime(date("Y-m-d"))));
        $file = 'Dashboard_ESMS_SI_S' . $num_sem . '.csv';
        $chemin = $backup . $file;
        
        $fp = fopen("$chemin", "w");
        $textFile = "#;Prefix;No. Ticket;Nature;Domaine;REF APP;NOM APP;IG;Description;Date envoi SMS;Début incident;Fin incident;Date_DEB-mois[1-12];Date_DEB-semaine[1-52];Date_DEB-jour[1-7];DEB-J[L-D];DEB-H[9h-18h];Date_FIN-mois[1-12];Date_FIN-semaine[1-52];Date_FIN-jour[1-7];FIN-J[L-D];FIN-H[9h-18h];SMS Spécifique \n";
        fwrite($fp, $textFile);
        /* Fin fichier joint */
        
        $body = "<font face='verdana' size='3'><b>Dashboard ESMS SI du <font color='#000080'>$dateFirstDay</font>  au  <font color='#000080'>$dateLastDay </font></b></font>";
        $body .= "<table class=\"migStyle\" border='1' width='97%' bordercolor='gray' cellpadding='1' cellspacing='0'>
	  <tr><th width='auto'><b>#</b></th>
	  <th width='auto'><font face='verdana' size='1'><b>Prefix</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>No. Ticket</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Nature</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Domaine</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>REF APP</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>NOM APP</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>IG</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Description</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date envoi SMS</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Début de l'incident</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Fin de l'incident</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_DEB-mois[1-12]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_DEB-semaine[1-52]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_DEB-jour[1-7]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>DEB-J[L-D]</b></font></th>	  
	  <th width='auto'><font face='verdana' size='1'><b>DEB-H[9h-18h]</b></font></th>	  
	  <th width='auto'><font face='verdana' size='1'><b>Date_FIN-mois[1-12]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_FIN-semaine[1-52]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_FIN-jour[1-7]</b></font></th>	  
	  <th width='auto'><font face='verdana' size='1'><b>FIN-J[L-D]</b></font></th>	  
	  <th width='auto'><font face='verdana' size='1'><b>FIN-H[9h-18h]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>SMS Spécifique</b></font></th></tr>";
        
        $result = $this->esmsApp->esmsIgsiHebdo($dateFirstDay, $dateLastDay);
        foreach ($result as $donnees_ticket) {
            $prefix_ticket = $donnees_ticket['prefix_ticket'];
            $id_ticket = $donnees_ticket['id_ticket'];
            $nature_com = $donnees_ticket['nature_com'];
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
            fwrite($fp, $textFile);
            $body .= "<td><font face='verdana' size='1'>$counter</font></td>
        		<td><font face='verdana' size='1'>$prefix_ticket</font></td>
        		<td><font face='verdana' size='1'>$id_ticket</font></td>
        		<td><font face='verdana' size='1'>$nature_com</font></td>
        		<td><font face='verdana' size='1'>$domaine</font></td>
        		<td><font face='verdana' size='1'>$ref_appli</font></td>
        		<td><font face='verdana' size='1'>$nom_appli</font></td>
        		<td><font face='verdana' size='1'>$ig</font></td>
        		<td><font face='verdana' size='1'>$description</font></td>";
            $body .= "
        		<td><font face='verdana' size='1'>$date_envoi_sms</font></td>
        		<td><font face='verdana' size='1'>$date_debut_inc</font></td>
        		<td><font face='verdana' size='1'>$date_fin_inc</font></td>
        		<td><font face='verdana' size='1'>$deb_month_inc</font></td>
        		<td><font face='verdana' size='1'>$deb_week_inc</font></td>
        		<td><font face='verdana' size='1'>$deb_day_inc</font></td>
        		<td><font face='verdana' size='1'>$debJ04</font></td>	 
        		<td><font face='verdana' size='1'>$debH918</font></td>	   
        		<td><font face='verdana' size='1'>$fin_month_inc</font></td>
        		<td><font face='verdana' size='1'>$fin_week_inc</font></td>
        		<td><font face='verdana' size='1'>$fin_day_inc</font></td>	 
        		<td><font face='verdana' size='1'>$finJ04</font></td>	 
        		<td><font face='verdana' size='1'>$finH918</font></td>	 		   
        		<td><font face='verdana' size='1'>$specifique_com</font></td>";
            $body .= "</tr>";
            $counter ++;
        }
        fclose($fp);
        $body .= "</table>";
        $body .= "<br><br>";
        
        // ***********************************************Fin partie dashboard moyenne GTR tickets du mois**********************************//
        /**
         * *****
         * Enregistrement du pourcentage acumulé dans le mois precedent.
         * *****
         */
        $annee = date("Y", strtotime("-1 week", strtotime(date("Y-m-d"))));
        $num_mois = date("n", strtotime("-1 week", strtotime(date("Y-m-d"))));
        $num_semaine = date("W", strtotime("-1 week", strtotime(date("Y-m-d"))));
        if ($num_mois == 1) {
            $mois = 'janvier';
        }
        if ($num_mois == 2) {
            $mois = 'fevrier';
        }
        if ($num_mois == 3) {
            $mois = 'mars';
        }
        if ($num_mois == 4) {
            $mois = 'avril';
        }
        if ($num_mois == 5) {
            $mois = 'mai';
        }
        if ($num_mois == 6) {
            $mois = 'juin';
        }
        if ($num_mois == 7) {
            $mois = 'juillet';
        }
        if ($num_mois == 8) {
            $mois = utf8_encode('août');
        }
        if ($num_mois == 9) {
            $mois = 'septembre';
        }
        if ($num_mois == 10) {
            $mois = 'octobre';
        }
        if ($num_mois == 11) {
            $mois = 'novembre';
        }
        if ($num_mois == 12) {
            $mois = 'decembre';
        }
        
        /*
         * $query_P = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','PLA','$pourcentageOkP','$OkPlatinium','$NokPlatinium')";
         * $this->igApp->doQuery($query_P);
         * $query_O = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','OR','$pourcentageOkO','$OkOr','$NokOr')";
         * $this->igApp->doQuery($query_O);
         * $query_A = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','ARG','$pourcentageOkA','$OkArgent','$NokArgent')";
         * $this->igApp->doQuery($query_A);
         * $query_BF = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','BEF','$pourcentageOkBF','$OkBestEffort','$NokBestEffort')";
         * $this->igApp->doQuery($query_BF);
         */
        /**
         * **
         * Fin enregistrement
         * **
         */
        
        // ***********************************************Mise a disposition du fichier dans le répertoire /igfixe/dataout/**********************************//
        $command = "cp $chemin $dataout" . "esms_hebdo_fixe.csv";
        exec($command);
        $command = "cp $chemin $dataout" . "esms_hebdo_si.csv";
        exec($command);
        // ***********************************************Fin mise a disposition du fichier hebdo**********************************//
        
        /**
         * ****
         * Envoi de mail hebdo
         * ****
         */
        
        $host = $this->config['igmensuel']['host'];
        
        $username = $this->config['igmensuel']['username']; // SMTP username
        $password = $this->config['igmensuel']['password']; // SMTP password
        $port = $this->config['igmensuel']['port'];
        
        $from = $this->config['igmensuel']['from'];
        $sender = $this->config['igmensuel']['sender'];
        $fromName = $this->config['igmensuel']['fromName'];
        $to = $this->config['igmensuel']['to'];
        $cc = $this->config['igmensuel']['cc'];
        
        $attachs[0] = [
            $chemin,
            $dataout
        ]; // fichier attaché
        
        $sujet = "Dashboard ESMS SI S$num_semaine $mois $annee";
        
        $this->mailer->sendPHPMailer($body, $sujet, $host, $username, $password, $port, $from, $sender, $fromName, $to, $cc, $attachs);
        
        echo "Done!, esmsigsihebdo";
    }

    public function esmsigsimensuelAction()
    {
        $request = $this->getRequest();
        
        // Make sure that we are running in a console, and the user has not
        // tricked our application into running this action from a public web
        // server:
        if (! $request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }
        
        // Récupèrer le chemin du backup
        $backup = $this->config['csv']['backup'];
        // Récuperer le chemin dataout
        $dataout = $this->config['csv']['dataout'];
        // Récuperer chemin de stockage fichier csv.
        $path = $this->config['csv']['extract']['path'];
        
        // // Cette fonction est dans le fichier -calcul_evolutionIg.php- elle mets à jour la table -evolution_ticket-
        // // et ça permets d'avoir l'évolution en fonction du temps
        $this->calculEvolutionIg->calculer_evolution_ticket();
        
        $counter = 1;
        $dateLastDay = date("Y-m-d H:i:s", strtotime("today -1 minute", strtotime(date("Y-m-d"))));
        $dateFirstDay = date("Y-m-d H:i:s", strtotime("last monday", strtotime(date("Y-m-d"))));
        
        /* Fichier joint */
        $num_sem = date("W", strtotime("-1 week", strtotime(date("Y-m-d"))));
        $file = 'Dashboard_ESMS_SI_S' . $num_sem . '.csv';
        $chemin = $backup . $file;
        $fp = fopen("$chemin", "w");
        $textFile = "#;Prefix;No. Ticket;Nature;Domaine;REF APP;NOM APP;IG;Description;Date envoi SMS;Début incident;Fin incident;Date_DEB-mois[1-12];Date_DEB-semaine[1-52];Date_DEB-jour[1-7];DEB-J[L-D];DEB-H[9h-18h];Date_FIN-mois[1-12];Date_FIN-semaine[1-52];Date_FIN-jour[1-7];FIN-J[L-D];FIN-H[9h-18h];SMS Spécifique \n";
        fwrite($fp, $textFile);
        /* Fin fichier joint */
        
        $body = "<font face='verdana' size='3'><b>Dashboard ESMS SI du <font color='#000080'>$dateFirstDay</font>  au  <font color='#000080'>$dateLastDay </font></b></font>";
        $body .= "<table class=\"migStyle\" border='1' width='97%' bordercolor='gray' cellpadding='1' cellspacing='0'>
	  <tr><th width='auto'><b>#</b></th>
	  <th width='auto'><font face='verdana' size='1'><b>Prefix</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>No. Ticket</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Nature</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Domaine</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>REF APP</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>NOM APP</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>IG</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Description</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date envoi SMS</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Début de l'incident</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Fin de l'incident</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_DEB-mois[1-12]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_DEB-semaine[1-52]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_DEB-jour[1-7]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>DEB-J[L-D]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>DEB-H[9h-18h]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_FIN-mois[1-12]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_FIN-semaine[1-52]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>Date_FIN-jour[1-7]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>FIN-J[L-D]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>FIN-H[9h-18h]</b></font></th>
	  <th width='auto'><font face='verdana' size='1'><b>SMS Spécifique</b></font></th></tr>";
        
        $result = $this->esmsApp->esmsIgsiMensuel($dateFirstDay, $dateLastDay);
        foreach ($result as $donnees_ticket) {
            $prefix_ticket = $donnees_ticket['prefix_ticket'];
            $id_ticket = $donnees_ticket['id_ticket'];
            $nature_com = $donnees_ticket['nature_com'];
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
            fwrite($fp, $textFile);
            $body .= "<td><font face='verdana' size='1'>$counter</font></td>
            <td><font face='verdana' size='1'>$prefix_ticket</font></td>
            <td><font face='verdana' size='1'>$id_ticket</font></td>
            <td><font face='verdana' size='1'>$nature_com</font></td>
            <td><font face='verdana' size='1'>$domaine</font></td>
            <td><font face='verdana' size='1'>$ref_appli</font></td>
            <td><font face='verdana' size='1'>$nom_appli</font></td>
            <td><font face='verdana' size='1'>$ig</font></td>
            <td><font face='verdana' size='1'>$description</font></td>";
            $body .= "
            <td><font face='verdana' size='1'>$date_envoi_sms</font></td>
            <td><font face='verdana' size='1'>$date_debut_inc</font></td>
            <td><font face='verdana' size='1'>$date_fin_inc</font></td>
            <td><font face='verdana' size='1'>$deb_month_inc</font></td>
            <td><font face='verdana' size='1'>$deb_week_inc</font></td>
            <td><font face='verdana' size='1'>$deb_day_inc</font></td>
            <td><font face='verdana' size='1'>$debJ04</font></td>
            <td><font face='verdana' size='1'>$debH918</font></td>
            <td><font face='verdana' size='1'>$fin_month_inc</font></td>
            <td><font face='verdana' size='1'>$fin_week_inc</font></td>
            <td><font face='verdana' size='1'>$fin_day_inc</font></td>
            <td><font face='verdana' size='1'>$finJ04</font></td>
            <td><font face='verdana' size='1'>$finH918</font></td>
            <td><font face='verdana' size='1'>$specifique_com</font></td>";
            $body .= "</tr>";
            $counter ++;
        }
        fclose($fp);
        $body .= "</table>";
        $body .= "<br><br>";
        
        // ***********************************************Fin partie dashboard moyenne GTR tickets du mois**********************************//
        /**
         * *****
         * Enregistrement du pourcentage acumulé dans le mois precedent.
         * *****
         */
        $annee = date("Y", strtotime("-1 month", strtotime(date("Y-m-d"))));
        $num_mois = date("n", strtotime("-1 month", strtotime(date("Y-m-d"))));
        if ($num_mois == 1) {
            $mois = 'janvier';
        }
        if ($num_mois == 2) {
            $mois = 'fevrier';
        }
        if ($num_mois == 3) {
            $mois = 'mars';
        }
        if ($num_mois == 4) {
            $mois = 'avril';
        }
        if ($num_mois == 5) {
            $mois = 'mai';
        }
        if ($num_mois == 6) {
            $mois = 'juin';
        }
        if ($num_mois == 7) {
            $mois = 'juillet';
        }
        if ($num_mois == 8) {
            $mois = utf8_encode('août');
        }
        if ($num_mois == 9) {
            $mois = 'septembre';
        }
        if ($num_mois == 10) {
            $mois = 'octobre';
        }
        if ($num_mois == 11) {
            $mois = 'novembre';
        }
        if ($num_mois == 12) {
            $mois = 'decembre';
        }
        
        /*
         * $query_P = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','PLA','$pourcentageOkP','$OkPlatinium','$NokPlatinium')";
         * $this->igApp->doQuery($query_P);
         * $query_O = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','OR','$pourcentageOkO','$OkOr','$NokOr')";
         * $this->igApp->doQuery($query_O);
         * $query_A = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','ARG','$pourcentageOkA','$OkArgent','$NokArgent')";
         * $this->igApp->doQuery($query_A);
         * $query_BF = "INSERT INTO suivi_gtr (annee,num_mois,mois,categorie_service,pourcentage,tok,tnok) VALUES('$annee','$num_mois','$mois','BEF','$pourcentageOkBF','$OkBestEffort','$NokBestEffort')";
         * $this->igApp->doQuery($query_BF);
         */
        /**
         * **
         * Fin enregistrement
         * **
         */
        
        /**
         * ****
         * Envoi de mail hebdo
         * ****
         */
        
        $host = $this->config['igmensuel']['host'];
        
        $username = $this->config['igmensuel']['username']; // SMTP username
        $password = $this->config['igmensuel']['password']; // SMTP password
        $port = $this->config['igmensuel']['port'];
        
        $from = $this->config['igmensuel']['from'];
        $sender = $this->config['igmensuel']['sender'];
        $fromName = $this->config['igmensuel']['fromName'];
        $to = $this->config['igmensuel']['to'];
        $cc = $this->config['igmensuel']['cc'];
        
        $attachs[0] = [
            $chemin,
            $file
        ]; // fichier attaché
        
        $sujet = "Dashboard ESMS SI $mois $annee";
        
        $this->mailer->sendPHPMailer($body, $sujet, $host, $username, $password, $port, $from, $sender, $fromName, $to, $cc, $attachs);
        
        echo "Done!";
    }
}
