<?php
namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;
use Zend\Db\Sql\Select;

/*
 * Valeur possible du champ "ticket_control.etat"
 * Etat Demande Création Ig: 10 ->annulation creation, 11 ->soumission création, 12->validation création; Etat Demande gel Ig: 20->annulation, 21->soumission,22->validation;Etat Demande degel Ig: 30->annulation, 31->soumission,32->validation;Etat Demande Cloture Ig: 40->annulation, 41->soumission,42->validation;Etat Demande Modif Ig: 50->annulation, 51->soumission,52->validation;
 *
 */
class TicketControl extends Base
{

    protected $table = 'ticket_control';

    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db;
        $this->initialize();
    }

    public function getNoComTicketsEncours()
    {
        return $this->select([
            'cloture' => 'NON',
            'sms' => true
        ]);
    }

    public function findByService($service)
    {
        return $this->select([
            'REF_Service' => $service,
            'cloture' => 'NON',
            'etat' => [
                12,
                20,
                21,
                22,
                30,
                31,
                32,
                40,
                41,
                42,
                50,
                51,
                52
            ]
        ]);
    }

    public function findByTicket($ticket)
    {
        return $this->select([
            'id_ticket' => $ticket,
            'cloture' => 'NON',
            'etat' => [
                12,
                20,
                21,
                22,
                30,
                31,
                32,
                40,
                41,
                42,
                50,
                51,
                52
            ]
        ]);
    }

    public function findOpen($param)
    {
        if (strpos($param, "''") === false)
            $param = str_replace("'", "''", $param);
        return $this->getAdapter()->fetchAll("SELECT *
												FROM ticket_control tc,esms_app s
												WHERE tc.typo_service=s.Nom AND
												cloture='NON' AND gele='NON' AND NOM LIKE '%" . $param . "%'
												AND tc.etat not in (11)
                                                ORDER BY id_ticket 
												limit 30
												");
    }

    public function findOuvert()
    {
        return $this->getAdapter()->fetchAll("SELECT *
												FROM ticket_control tc,esms_app s
												WHERE tc.typo_service=s.Nom AND
												cloture='NON' AND gele='NON' 
                                                AND tc.etat not in (11)
												ORDER BY id_ticket");
    }

    public function findClosed($param)
    {
        if (strpos($param, "''") === false)
            $param = str_replace("'", "''", $param);
        return $this->getAdapter()->fetchAll("SELECT *
												FROM ticket_control tc,esms_app s
												WHERE tc.typo_service=s.Nom AND cloture='OUI' AND NOM LIKE '%" . $param . "%'
                                                AND tc.etat not in (11)
												ORDER BY debut_incident DESC
												limit 30");
    }

    public function findClos()
    {
        return $this->getAdapter()->fetchAll("SELECT *
												FROM ticket_control tc,esms_app s
												WHERE tc.typo_service=s.Nom AND cloture='OUI'
												ORDER BY debut_incident DESC
												limit 60");
    }

    public function findAcloturer()
    {
        return $this->getAdapter()->fetchAll("
								SELECT *
								FROM ticket_control tc,esms_app s
								WHERE tc.typo_service=s.Nom AND cloture='NON'
                                                                AND tc.etat not in (11)
								ORDER BY id_ticket");
    }

    public function findAgeler()
    {
        return $this->fetchAll($this->select(function (Select $select) {
            $select->where([
                'cloture' => 'NON',
                'gele' => 'NON',
                'etat' => [
                    12,
                    20,
                    21,
                    22,
                    30,
                    31,
                    32,
                    40,
                    41,
                    42,
                    50,
                    51,
                    52
                ]
            ]);
            $select->order('id_ticket');
        }));
    }

    public function findAdegeler()
    {
        return $this->fetchAll($this->select(function (Select $select) {
            $select->where([
                'cloture' => 'NON',
                'gele' => 'OUI',
                'etat' => [
                    12,
                    20,
                    21,
                    22,
                    30,
                    31,
                    32,
                    40,
                    41,
                    42,
                    50,
                    51,
                    52
                ]
            ]);
            $select->order('id_ticket');
        }));
    }

    public function findAsupprimer()
    {
        return $this->fetchAll($this->select(function (Select $select) {
            $select->where([
                'etat' => [
                    11,
                    12,
                    20,
                    21,
                    22,
                    30,
                    31,
                    32,
                    40,
                    41,
                    42,
                    50,
                    51,
                    52
                ]
            ]);
            $select->order('id_ticket DESC');
            $select->limit(60);
        }));
    }

    public function findAdescoper()
    {
        return $this->fetchAll($this->select(function (Select $select) {
            $select->where([
                'cloture' => 'OUI',
                'etat' => [
                    12,
                    20,
                    21,
                    22,
                    30,
                    31,
                    32,
                    40,
                    41,
                    42,
                    50,
                    51,
                    52
                ]
            ]);
            $select->order('id_ticket DESC');
            $select->limit(60);
        }));
    }

    public function dashboard($idTicket = NULL, $categorie = NULL, $apli = NULL, $si = NULL, $desc_incident = NULL, $debut_incident = NULL, $proc_evo = NULL, $ig_depart = NULL, $ig_encours = NULL, $ig_proc = NULL, $pilote = NULL, $tri = NULL)
    {
        if (isset($tri)) {
            switch ($tri) {
                case "idTicket":
                    $whTri = "ticket_control.id_ticket ASC ";
                    break;
                case "idTicket_desc":
                    $whTri = "ticket_control.id_ticket DESC ";
                    break;
                case "idCategorie":
                    $whTri = "ticket_control.categorie_service ASC ";
                    break;
                case "idCategorie_desc":
                    $whTri = "ticket_control.categorie_service DESC ";
                    break;
                case "apli":
                    $whTri = "ticket_control.typo_service ASC ";
                    break;
                case "apli_desc":
                    $whTri = "ticket_control.typo_service DESC ";
                    break;
                case "idSi":
                    $whTri = "ticket_control.SI ASC ";
                    break;
                case "idSi_desc":
                    $whTri = "ticket_control.SI DESC ";
                    break;
                case "desc_incident":
                    $whTri = "commentaire ASC ";
                    break;
                case "desc_incident_desc":
                    $whTri = "commentaire DESC ";
                    break;
                case "debut_incident":
                    $whTri = "ticket_control.debut_incident ASC ";
                    break;
                case "debut_incident_desc":
                    $whTri = "ticket_control.debut_incident DESC ";
                    break;
                case "proc_evo":
                    $whTri = "evolution_ticket.date_prochaine_incident ASC ";
                    break;
                case "proc_evo_desc":
                    $whTri = "evolution_ticket.date_prochaine_incident DESC ";
                    break;
                case "ig_depart":
                    $whTri = "evolution_ticket.IG_debut ASC ";
                    break;
                case "ig_depart_desc":
                    $whTri = "evolution_ticket.IG_debut DESC ";
                    break;
                case "ig_encours":
                    $whTri = "evolution_ticket.IG_encours ASC ";
                    break;
                case "ig_encours_desc":
                    $whTri = "evolution_ticket.IG_encours DESC ";
                    break;
                case "impact":
                    $whTri = "evolution_ticket.duree_incident ASC ";
                    break;
                case "impact_desc":
                    $whTri = "evolution_ticket.duree_incident DESC ";
                    break;
                case "ig_proc":
                    $whTri = "evolution_ticket.IG_prochaine ASC ";
                    break;
                case "ig_proc_desc":
                    $whTri = "evolution_ticket.IG_prochaine DESC ";
                    break;
                case "pilote":
                    $whTri = "ticket_control.nom_admin ASC ";
                    break;
                case "pilote_desc":
                    $whTri = "ticket_control.nom_admin DESC ";
                    break;
            }
        } else
            $whTri = "ticket_control.debut_incident DESC ";
        $whTi = ($idTicket == "") ? " " : " AND ticket_control.id_ticket LIKE '%" . $idTicket . "%'";
        $whCat = ($categorie == "") ? " " : " AND ticket_control.categorie_service = '" . $categorie . "'";
        $whApli = ($apli == "") ? " " : " AND LOWER(ticket_control.typo_service) LIKE '%" . $apli . "%'";
        $whSi = ($si == "") ? " " : " AND  ticket_control.SI  LIKE '%" . $si . "%' ";
        $whDebutIncident = ($debut_incident == "") ? " " : " AND ticket_control.debut_incident >= '" . $debut_incident . "'";
        $whProcEvo = ($proc_evo == "") ? " " : " AND  evolution_ticket.date_prochaine_incident >=  '" . $proc_evo . "'";
        $whIgDepart = ($ig_depart == "" || $ig_depart == "0") ? " " : " AND evolution_ticket.IG_debut LIKE '" . $ig_depart . "'";
        $whIgEnCours = ($ig_encours == "" || $ig_encours == "0") ? " " : " AND evolution_ticket.IG_encours LIKE '" . $ig_encours . "'";
        $whIgProc = ($ig_proc == "" || $ig_proc == "0") ? " " : " AND evolution_ticket.IG_prochaine LIKE '" . $ig_proc . "'";
        $whPilote = ($pilote == "") ? " " : " AND ticket_control.nom_admin LIKE '%" . $pilote . "%'";
        
        $where = $whTi . $whCat . $whApli . $whSi . $whDebutIncident . $whProcEvo . $whIgDepart . $whIgEnCours . $whIgProc . $whPilote;
        
        $sql = "SELECT TypeIG,
					   ticket_control.id_ticket, 
					   ticket_control.typo_service, 
					   ticket_control.debut_incident, 
					   ticket_control.categorie_service, 
					   ticket_control.cloture,  
					   ticket_control.nom_admin, 
					   ticket_control.sous_systeme, 
					   ticket_control.msg_pronet, 
					   ticket_control.gele,
					   ticket_control.SI, 
                       ticket_control.etat,
                       ticket_control.sms,
					   evolution_ticket.duree_incident_creation,  
					   evolution_ticket.IG_debut, 
					   evolution_ticket.IG_encours, 
					   evolution_ticket.duree_incident, 
					   evolution_ticket.date_prochaine_incident, 
					   evolution_ticket.IG_prochaine,
					   commentaire
				FROM ticket_control, 
					 evolution_ticket 
				WHERE ticket_control.id_ticket = evolution_ticket.id_ticket 
				AND ticket_control.cloture = 'NON' " . $where . "  
				ORDER BY ticket_control.categorie_service IN ('Best Effort'), " . $whTri;
        
        return $this->getAdapter()->fetchAll($sql);
    }

    public function allCount()
    {
        $sql = "SELECT count(*) as count
		FROM ticket_control,  evolution_ticket 
		WHERE ticket_control.id_ticket=evolution_ticket.id_ticket AND ticket_control.cloture='NON' 
        AND ticket_control.etat not in (11)
		ORDER BY ticket_control.categorie_service IN('Best Effort') ";
        return $this->getAdapter()->fetchAll($sql);
    }

    public function numCategorie($categorie_service, $dateInitial, $dateFinal)
    {
        return $this->fetchAll("SELECT * FROM ticket_control WHERE debut_incident = '$dateInitial' 
    		    AND date_cloture_incident = '$dateFinal' AND  categorie_service = '$categorie_service' AND cloture = 'OUI'
    		    AND dsko_ig = 0 
                    AND etat not in (11) ORDER BY id_ticket DESC ;"
                        /* $this->select(function(Select $select){
                          $select->where(["debut_incident" => "$dateInitial", "date_cloture_incident"=>"$dateFinal",
                          "categorie_service" => "$categorie_service", "cloture" =>'OUI', "dsko_ig" => '0']);
                          $select->order('id_ticket DESC');
                          }) */
        );
    }

    public function numGTRok($categorie_service, $gtr, $dateInitial, $dateFinal)
    {
        return $this->getAdapter()->fetchAll("SELECT tc.id_ticket, tc.duree_incident, et.id_ticket 
							FROM ticket_control as tc, evolution_ticket as et 
							WHERE ((tc.id_ticket=et.id_ticket)AND(tc.debut_incident>='$dateInitial')
							AND(tc.debut_incident<='$dateFinal')
							AND(tc.categorie_service='$categorie_service')AND(tc.duree_incident<'$gtr')
							AND(tc.cloture='OUI')AND(tc.dsko_ig='0')
                                                        AND tc.etat not in (11))
							");
    }

    public function numGTRnok($categorie_service, $gtr, $dateInitial, $dateFinal)
    {
        return $this->getAdapter()->fetchAll("SELECT tc.id_ticket, tc.duree_incident, et.id_ticket 
							FROM ticket_control as tc, evolution_ticket as et 
							WHERE ((tc.id_ticket=et.id_ticket)AND(tc.debut_incident>='$dateInitial')
							AND(tc.debut_incident<='$dateFinal')AND(tc.categorie_service='$categorie_service')
							AND(tc.duree_incident>='$gtr')AND(tc.cloture='OUI')AND(tc.dsko_ig='0')
                                                        AND tc.etat not in (11))
							");
    }

    public function getMoyenneGTR($categorie_service, $gtr, $dateInitial, $dateFinal)
    {
        return $this->getAdapter()->fetchAll("SELECT tc.id_ticket, tc.duree_incident, et.id_ticket 
							FROM ticket_control as tc, evolution_ticket as et 
							WHERE ((tc.id_ticket=et.id_ticket)AND(tc.debut_incident>='$dateInitial')
							AND(tc.debut_incident<='$dateFinal')AND(tc.categorie_service='$categorie_service')
							AND(tc.cloture='OUI')AND(tc.dsko_ig='0')
                                                        AND tc.etat not in (11))
							");
    }

    public function getMoyenneGTRDate($categorie_service, $dateInitial, $dateFinal)
    {
        return $this->getAdapter()->fetchAll("SELECT tc.id_ticket, tc.date_cloture_incident, tc.debut_incident 
							FROM ticket_control as tc 
							WHERE ((tc.debut_incident>='$dateInitial')
							AND(tc.debut_incident<='$dateFinal')AND(tc.categorie_service='$categorie_service')
							AND(tc.cloture='OUI')AND(tc.dsko_ig='0')
                            AND tc.etat not in (11))
							");
    }

    public function getNumDescope($categorie_service, $dateInitial, $dateFinal)
    {
        return $this->getAdapter()->fetchAll("SELECT tc.id_ticket 
							FROM ticket_control as tc 
							WHERE ((tc.debut_incident>='$dateInitial')AND(tc.debut_incident<='$dateFinal')
							AND(tc.categorie_service='$categorie_service')AND(tc.dsko_ig='1')
                            AND tc.etat not in (11)))
							");
    }

    public function calculer_evolution_ticket()
    {
        return $this->getAdapter()->fetchAll("SELECT TypeIG,ticket_control.id_ticket, ticket_control.date_creation_incident, ticket_control.categorie_service, ticket_control.debut_incident, ticket_control.nb_service, ticket_control.etat_service, ticket_control.visibilite, ticket_control.busy_hour, ticket_control.IG, ticket_control.cloture, ticket_control.date_cloture_incident, ticket_control.date_creation_cloture, ticket_control.gele, ticket_control.date_gel_incident, ticket_control.date_creation_gel, categorie_services.id_typo as id_typo, etat_services.id_etat as id_etat 
								,duree_incident
								FROM ticket_control, categorie_services, etat_services 
								WHERE ticket_control.categorie_service=categorie_services.typo_categorie  
								AND ticket_control.etat_service=etat_services.etat 
                                AND ticket_control.cloture='NON'
                                AND ticket_control.etat not in (11)");
    }

    public function IGEnCours()
    {
        $date_moins_40 = date("Y-m-d H:i:s", strtotime("-40 days"));
        // AND (ticket_control.debut_incident BETWEEN (ADDDATE(curdate(),INTERVAL -40 DAY)) AND curdate())
        return $this->getAdapter()->fetchAll("SELECT ticket_control.id_ticket, 
													 ticket_control.typo_service, 
													 ticket_control.debut_incident, 
													 ticket_control.categorie_service, 
													 ticket_control.cloture,  
													 ticket_control.nom_admin, 
													 ticket_control.sous_systeme, 
													 ticket_control.gele, 
													 ticket_control.dsko_ig, 
													 ticket_control.date_cloture_incident, 
													 ticket_control.IG, 
													 ticket_control.date_sms_ouverture,
													 ticket_control.sms_commentaire,
													 evolution_ticket.duree_incident_creation, 
													 evolution_ticket.IG_debut, 
													 evolution_ticket.IG_encours, 
													 evolution_ticket.duree_incident, 
													 evolution_ticket.date_prochaine_incident, 
													 evolution_ticket.IG_prochaine 
											FROM 	 ticket_control, evolution_ticket 
											WHERE 	 ticket_control.id_ticket=evolution_ticket.id_ticket 
											AND 	 ticket_control.debut_incident >= '" . $date_moins_40 . "'
                                                                                        AND      ticket_control.etat not in (11)
											ORDER BY ticket_control.cloture,
													 ticket_control.debut_incident DESC, 
													 ticket_control.date_cloture_incident DESC,
													 ticket_control.categorie_service IN('Best Effort'),
													 ticket_control.categorie_service DESC,
													 ticket_control.id_ticket DESC");
    }

    public function IGEnCoursApp($service)
    {
        return $this->getAdapter()->fetchAll("SELECT ticket_control.id_ticket
											FROM 	 ticket_control, evolution_ticket 
											WHERE 	 ticket_control.id_ticket=evolution_ticket.id_ticket 
                                            AND ticket_control.REF_Service ='$service'
                                            AND ticket_control.etat not in (11)
											ORDER BY ticket_control.cloture,
													 ticket_control.debut_incident DESC, 
													 ticket_control.date_cloture_incident DESC,
													 ticket_control.categorie_service IN('Best Effort'),
													 ticket_control.categorie_service DESC,
													 ticket_control.id_ticket DESC");
    }

    public function IGEnCoursAppValide($service)
    {
        return $this->getAdapter()->fetchAll("SELECT ticket_control.id_ticket
            FROM 	 ticket_control, evolution_ticket
            WHERE 	 ticket_control.id_ticket=evolution_ticket.id_ticket
            AND ticket_control.cloture = 'NON'
            AND ticket_control.etat = 12
            AND ticket_control.REF_Service ='$service'
            ORDER BY ticket_control.cloture,
            ticket_control.debut_incident DESC,
            ticket_control.date_cloture_incident DESC,
            ticket_control.categorie_service IN('Best Effort'),
            ticket_control.categorie_service DESC,
            ticket_control.id_ticket DESC");
    }

    public function IgEnAttente()
    {
        return $this->getAdapter()->fetchAll("SELECT ticket_control.id_ticket
            FROM 	 ticket_control, evolution_ticket
            WHERE 	 ticket_control.id_ticket=evolution_ticket.id_ticket
            AND ticket_control.cloture = 'NON'
            AND ticket_control.etat in (11,21,31,41,51)
            ORDER BY ticket_control.cloture,
            ticket_control.debut_incident DESC,
            ticket_control.date_cloture_incident DESC,
            ticket_control.categorie_service IN('Best Effort'),
            ticket_control.categorie_service DESC,
            ticket_control.id_ticket DESC");
    }

    public function getTwo()
    {
        $sql = "SELECT typo_service FROM ticket_control  WHERE ticket_control.etat not in (11) limit 10";
        return $this->getAdapter()->fetchAll($sql);
    }

    public function findAll()
    {
        return $this->getAdapter()->fetchAll('SELECT * FROM ticket_control WHERE ticket_control.etat not in (11) ORDER BY id_ticket');
    }

    public function exec($query)
    {
        return $this->getAdapter()->fetchAll($query);
    }

    public function ADescoper()
    {
        return $this->getAdapter()->fetchAll('SELECT * FROM ticket_control WHERE cloture="OUI" AND dsko_ig=0 AND ticket_control.etat not in (11)  ORDER BY id_ticket');
    }

    public function doRequest($formData)
    {
        $query = 'SELECT * FROM view_requete';
        // Le ticket cloturé ou non
        if ($formData['radio_cloture'] == 'oui')
            $query .= ' WHERE (cloture = \'OUI\') ';
        else 
            if ($formData['radio_cloture'] == 'non')
                $query .= ' WHERE (cloture = \'NON\') ';
            else
                $query .= ' WHERE (cloture = \'OUI\' OR cloture = \'NON\') ';
            
            // Les dates de début et de fin
            // Les IG initials et finals
            // Le numéro de ticket
        if (isset($formData['dateDebut']) && $formData['dateDebut'] != '')
            $query .= ' AND (debut_incident >= \'' . $formData['dateDebut'] . " 00-00-00" . '\') ';
        if (isset($formData['dateFin']) && $formData['dateFin'] != '')
            $query .= ' AND (debut_incident <= \'' . $formData['dateFin'] . " 23-59-59" . '\') ';
        if (isset($formData['igInitial']) && $formData['igInitial'] != '')
            $query .= ' AND ((IG>=\'' . $formData['igInitial'] . '\') OR (IG_debut>=\'' . $formData['igInitial'] . '\')) ';
        if (isset($formData['igFinal']) && $formData['igFinal'] != '')
            $query .= ' AND ((IG<=\'' . $formData['igFinal'] . '\') OR (IG_prochaine<=\'' . $formData['igFinal'] . '\')) ';
        if (isset($formData['id_ticket']) && $formData['id_ticket'] != '')
            $query .= ' AND (id_ticket LIKE \'%' . $formData['id_ticket'] . '%\') ';
            
            // Tester les services
        $tab = array();
        $recherche_detail = false;
        $recherche_tout = false;
        $detail_service = "";
        $categorie = "";
        
        if ($formData['check_platinium'] != 1 && isset($formData['Platinium']) && $formData['Platinium'])
            $tab = array_merge($tab, explode(",", $formData['Platinium']));
        
        if ($formData['check_or'] != 1 && isset($formData['Or']) && $formData['Or'])
            $tab = array_merge($tab, explode(",", $formData['Or']));
        
        if ($formData['check_argent'] != 1 && isset($formData['Argent']) && $formData['Argent'])
            $tab = array_merge($tab, explode(",", $formData['Argent']));
        
        if ($formData['check_bronze'] != 1 && isset($formData['BestEffort']) && $formData['BestEffort'])
            $tab = array_merge($tab, explode(",", $formData['BestEffort']));
        
        if (isset($tab) && sizeof($tab) > 0) {
            $recherche_detail = true;
            for ($i = 0; $i < sizeof($tab); $i ++) {
                if ($i == 0)
                    $detail_service .= " ( ";
                else
                    $detail_service .= ' OR ';
                $detail_service .= ' NOM = "' . $tab[$i] . '" ';
            }
            $detail_service .= " ) ";
        }
        
        // Par catégorie si on coche sur "selectionner tout"
        if ($formData['check_platinium'] == 1 || $formData['check_or'] == 1 || $formData['check_argent'] == 1 || $formData['check_bronze'] == 1) {
            $recherche_tout = true;
            if ($formData['check_platinium'] == 1)
                $categorie .= " categorie_service = 'Platinium' ";
            if ($formData['check_or'] == 1) {
                if ($formData['check_platinium'] == 1)
                    $categorie .= "OR";
                $categorie .= " categorie_service = 'Or' ";
            }
            if ($formData['check_argent'] == 1) {
                if ($formData['check_platinium'] == 1 || $formData['check_or'] == 1)
                    $categorie .= "OR";
                $categorie .= " categorie_service = 'Argent' ";
            }
            if ($formData['check_bronze'] == 1) {
                if ($formData['check_platinium'] == 1 || $formData['check_or'] == 1 || $formData['check_argent'] == 1)
                    $categorie .= "OR";
                $categorie .= " categorie_service = 'Bronze' ";
            }
        }
        
        if ($recherche_detail != false || $recherche_tout != false) {
            $query .= " AND (";
            if ($detail_service != "")
                $query .= "( " . $detail_service . " )";
            if ($recherche_tout == true && (($formData['check_platinium'] != 1 && isset($formData['Platinium']) && $formData['Platinium'] != "") || ($formData['check_or'] != 1 && isset($formData['Or']) && $formData['Or'] != "") || ($formData['check_argent'] != 1 && isset($formData['Argent']) && $formData['Argent'] != "") || ($formData['check_bronze'] != 1 && isset($formData['BestEffort']) && $formData['BestEffort'] != "")))
                $query .= " OR ";
            if ($recherche_tout == true)
                $query .= "( " . $categorie . " )";
            
            $query .= ")";
        }
        
        $query .= ' GROUP BY id_ticket';
        $query .= ' ORDER BY debut_incident DESC,categorie_service DESC,id_ticket DESC ';
        
        return $this->getAdapter()->fetchAll($query);
    }
}
