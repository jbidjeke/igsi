<?php
namespace Cron\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;
/**
 * Model igsi
 * 
 * @author Jean eric et cat
 *
 */
class IgApp extends BaseApp
{

    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db;
        $this->initialize();
         
    }

    

    public function updateIgsiEsmsAppWeb()
    {
        $PLA_query = $this->doQuery("update esms_app set SI='SI' where REF in ('SVC0018','SVC0019','SVC0030','SVC0049','SVC0064','SVC0065','SVC0084','SVC0085')");
        $OR_query = $this->doQuery("update esms_app set SI='SI' where REF in ('SVC0046','SVC0063','SVC0133')");
        $ARG_query = $this->doQuery("update esms_app set SI='SI' where REF in ('SVC0026','SVC0036','SVC0060','SVC0061','SVC0087','SVC0099','SVC0117','SVC0118','SVC0119','SVC0126')");
        $BE_query = $this->doQuery("update esms_app set SI='SI' where REF in ('SVC0023','SVC0130')");
    
    }
    
    /*public function insertEsmsApp($fichier)
    {
        $truncate_query = $this->doQuery("TRUNCATE TABLE esms_app");
        $insertion_query = $this->doQuery(addcslashes("LOAD DATA LOCAL INFILE '$fichier' INTO TABLE esms_app CHARACTER SET latin1 FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n' IGNORE 1 LINES", "\\"));
    }*/
    

    /*public function checkBd($nombase)
    {
        try{
            $test_query = $this->fetchAllToArray("SELECT COUNT(*) FROM $nombase");
            if(count($test_query)==0)
            {
                return false;
            }
            else
            {
                return true;
            }
        }catch (\Exception $e){
            return false;
        }
        
    }*/
    
    
    public function insertEsmsAppServices($fichier)
    {
        $truncate_query = $this->doQuery("TRUNCATE TABLE services");
        $insertion_query = $this->doQuery("LOAD DATA LOCAL INFILE '$fichier' INTO TABLE services CHARACTER SET latin1 FIELDS TERMINATED BY ';' LINES TERMINATED BY '\n' IGNORE 1 LINES");
    }
    
    /*public function updateEsmsAppWeb()
    {
        $PLA_query = $this->doQuery("update esms_app set SI='SI', SI_ID=1 where REF in ('SVC0019','SVC0049','SVC0064','SVC0065','SVC0084')");
        $OR_query = $this->doQuery("update esms_app set SI='SI', SI_ID=1 where REF in ('SVC0046','SVC0096','SVC0133')");
        $ARG_query = $this->doQuery("update esms_app set SI='SI', SI_ID=1 where REF in ('SVC0026','SVC0036','SVC0060','SVC0061','SVC0087','SVC0099','SVC0118','SVC0126')");
        $BE_query = $this->doQuery("update esms_app set SI='SI', SI_ID=1 where REF in ('SVC0023','SVC0130')");
    
    }
    
    public function updateEsmsAppSpp()
    {
        $futur_buzz_query = $this->doQuery("update esms_app set SI='SI', SI_ID=1 where REF in ('APP2707','APP2708')");
        $appli_bol_query = $this->doQuery("update esms_app set SI='SI', SI_ID=1 where REF in ('APP1642','APP1980','APP1983')");
    
    }*/
    
    public function ighebdo($dateFirstDay, $dateLastDay){
        /*$dateLastDay = "2013-06-04 00:30:00";
        $dateFirstDay = "2013-06-05 01:30:00";*/
        
        $result = $this->fetchAllToArray("SELECT ticket_control.id_ticket, ticket_control.typo_service, ticket_control.debut_incident,
            	ticket_control.date_cloture_incident, ticket_control.categorie_service, ticket_control.cloture, 
            	ticket_control.nom_admin, ticket_control.sous_systeme, evolution_ticket.IG_debut, 
            	ticket_control.duree_incident, ticket_control.IG_debut AS IG_ctrlDeb, ticket_control.IG,  
            	historique_mail.id_ticket, historique_mail.date_mail_ouverture, historique_mail.mail_suivi,
            	historique_mail.date_mail_cloture 
            	FROM ticket_control, historique_mail, evolution_ticket 
            	WHERE (ticket_control.id_ticket=historique_mail.id_ticket AND ticket_control.id_ticket=evolution_ticket.id_ticket AND ticket_control.debut_incident>='$dateFirstDay' AND ticket_control.debut_incident<='$dateLastDay' AND ticket_control.etat not in (11)) OR (ticket_control.id_ticket=historique_mail.id_ticket AND ticket_control.id_ticket=evolution_ticket.id_ticket AND ticket_control.debut_incident>='$dateFirstDay' AND ticket_control.debut_incident<='$dateLastDay') OR (ticket_control.id_ticket=historique_mail.id_ticket AND ticket_control.id_ticket=evolution_ticket.id_ticket AND ticket_control.debut_incident>='$dateFirstDay' AND ticket_control.debut_incident<='$dateLastDay') 
            	ORDER BY ticket_control.categorie_service IN('Best Effort'),ticket_control.categorie_service DESC,ticket_control.id_ticket DESC");
    
        //var_dump($result);
        return $result;
    }
    
    public function igmensuel($dateFirstDay, $dateLastDay){
        return $this->fetchAllToArray("SELECT ticket_control.id_ticket, ticket_control.typo_service, ticket_control.debut_incident,
        	ticket_control.date_cloture_incident, ticket_control.categorie_service, ticket_control.cloture, 
        	ticket_control.nom_admin, ticket_control.sous_systeme, evolution_ticket.IG_debut, 
        	ticket_control.duree_incident, ticket_control.IG_debut AS IG_ctrlDeb, ticket_control.IG,  
        	historique_mail.id_ticket, historique_mail.date_mail_ouverture, historique_mail.mail_suivi,
        	historique_mail.date_mail_cloture 
        	FROM ticket_control, historique_mail, evolution_ticket 
        	WHERE (ticket_control.id_ticket=historique_mail.id_ticket AND ticket_control.id_ticket=evolution_ticket.id_ticket AND ticket_control.debut_incident>='$dateFirstDay' AND ticket_control.debut_incident<='$dateLastDay' AND ticket_control.etat not in (11)) OR (ticket_control.id_ticket=historique_mail.id_ticket AND ticket_control.id_ticket=evolution_ticket.id_ticket AND ticket_control.debut_incident>='$dateFirstDay' AND ticket_control.debut_incident<='$dateLastDay') OR (ticket_control.id_ticket=historique_mail.id_ticket AND ticket_control.id_ticket=evolution_ticket.id_ticket AND ticket_control.debut_incident>='$dateFirstDay' AND ticket_control.debut_incident<='$dateLastDay') 
        	ORDER BY ticket_control.categorie_service IN('Best Effort'),ticket_control.categorie_service DESC,ticket_control.id_ticket DESC");
    }
    
    
    
    public function extractIgEcHebdo($date_debut, $date_fin){
    
    
        return $this->fetchAllToArray("SELECT ticket_control.id_ticket, 
												   ticket_control.typo_service, 
												   ticket_control.debut_incident,
												   ticket_control.date_cloture_incident, 
												   ticket_control.categorie_service, 
												   ticket_control.cloture, 
												   ticket_control.nom_admin, 
												   ticket_control.sous_systeme, 
												   ticket_control.duree_incident, 
												   ticket_control.IG_debut AS IG_ctrlDeb, 
												   ticket_control.IG, 
												   evolution_ticket.IG_debut, 
												   historique_mail.id_ticket, 
												   historique_mail.date_mail_ouverture, 
												   historique_mail.mail_suivi,
												   historique_mail.date_mail_cloture 
											FROM ticket_control, 
												 historique_mail, 
												 evolution_ticket 
											WHERE ticket_control.id_ticket = historique_mail.id_ticket 
											AND ticket_control.id_ticket = evolution_ticket.id_ticket 
											AND ticket_control.debut_incident >= '".$date_debut."' 
											AND ticket_control.debut_incident <= '".$date_fin."' 
                                            AND ticket_control.etat not in (11)
											ORDER BY ticket_control.categorie_service IN('Best Effort'),
												     ticket_control.categorie_service DESC, 
												     ticket_control.id_ticket DESC");
    }
    
    public function extractIgHebdo($date_debut, $date_fin){
    
    
        return $this->fetchAllToArray("SELECT ticket_control.id_ticket, 
												   ticket_control.typo_service, 
												   ticket_control.debut_incident,
												   ticket_control.date_cloture_incident, 
												   ticket_control.categorie_service, 
												   ticket_control.cloture, 
												   ticket_control.nom_admin, 
												   ticket_control.sous_systeme, 
												   ticket_control.duree_incident, 
												   ticket_control.IG_debut AS IG_ctrlDeb, 
												   ticket_control.IG, 
												   evolution_ticket.IG_debut, 
												   historique_mail.id_ticket, 
												   historique_mail.date_mail_ouverture, 
												   historique_mail.mail_suivi,
												   historique_mail.date_mail_cloture 
											FROM ticket_control, 
												 historique_mail, 
												 evolution_ticket 
											WHERE ticket_control.id_ticket = historique_mail.id_ticket 
											AND ticket_control.id_ticket = evolution_ticket.id_ticket 
											AND ticket_control.debut_incident >= '".$date_debut."' 
											AND ticket_control.debut_incident <= '".$date_fin."' 
                                            AND ticket_control.etat not in (11)
											ORDER BY ticket_control.categorie_service IN('Best Effort'),
												     ticket_control.categorie_service DESC, 
												     ticket_control.id_ticket DESC");
    }
    
    public function extractSixMonths($date_debut, $date_fin){
        $query = "SELECT `tc`.`id_ticket` AS `id_ticket`,
					`tc`.`typo_service` AS `typo_service`,
					`tc`.`date_creation_incident` AS `date_creation_incident`,
					`tc`.`debut_incident` AS `debut_incident`,
					`tc`.`date_creation_cloture` AS `date_creation_cloture`,
					`tc`.`date_cloture_incident` AS `date_cloture_incident`,
					`tc`.`categorie_service` AS `categorie_service`,
					`tc`.`IG_debut` AS `IG_ctrlDeb`,
					`tc`.`IG` AS `IG`,
					`tc`.`cloture` AS `cloture`,
					`tc`.`dsko_ig` AS `dsko_ig`,
					`tc`.`dsko_commentaire` AS `dsko_commentaire`,
					`tc`.`nom_admin` AS `nom_admin`,
					`tc`.`duree_incident` AS `ticket_duree_incident`,
					`tc`.`sms_commentaire` AS `sms_commentaire`,
					`tc`.`SI` AS `SI`,
					`tc`.`CommentaireIG` AS `CommentaireIG`,
					`tc`.`Commentaire` AS `descincident`,
					`tc`.`date_sms_ouverture` AS `date_sms_ouverture`,
					`tc`.`etat_service` AS `etat_service`,
					`cg`.`gele` AS `gele`,
					`cg`.`duree_gel` AS `duree_gel`,
					`cg`.`manager_astreinte` AS `manager_astreinte`,
					`et`.`IG_debut` AS `IG_debut`,
					`et`.`IG_encours` AS `IG_encours`,
					`et`.`IG_prochaine` AS `IG_prochaine`,
					`et`.`duree_incident` AS `ev_duree_incident`,
					`hm`.`mail_ouverture` AS `mail_ouverture`,
					`hm`.`mail_suivi` AS `mail_suivi`,
					`hm`.`mail_cloture` AS `mail_cloture`,
					`cs`.`id_typo` AS `id_typo`,
					`cs`.`typo_categorie` AS `typo_categorie`,
					`ser`.`NOM` AS `NOM`,
					group_concat(`m`.`IG`,_utf8': ',`m`.`commentaire` separator ',') AS `commentaire`
			FROM ((((((`ticket_control` `tc` left join `message` `m` on((`tc`.`id_ticket` = `m`.`id_ticket`)))
			JOIN `control_gel` `cg` on((`tc`.`id_ticket` = `cg`.`id_ticket`)))
			JOIN `historique_mail` `hm` on((`tc`.`id_ticket` = `hm`.`id_ticket`)))
			JOIN `evolution_ticket` `et` on((`tc`.`id_ticket` = `et`.`id_ticket`)))
			JOIN `categorie_services` `cs` on((`tc`.`categorie_service` = `cs`.`typo_categorie`)))
			JOIN `services` `ser` on((`tc`.`typo_service` = `ser`.`NOM`)))";
        /*group by `tc`.`id_ticket`
         order by `tc`.`debut_incident` desc,`tc`.`categorie_service` desc,`tc`.`id_ticket` desc";*/
        $query .=" WHERE `tc`.`debut_incident` >= '".$date_debut."'";
        $query .=" AND `tc`.`debut_incident` <= '".$date_fin."'";
        $query .= " AND tc.etat not in (11)";
        $query .= ' GROUP BY id_ticket';
        $query .= ' ORDER BY debut_incident DESC,categorie_service DESC,id_ticket DESC ';
    
        return $this->fetchAllToArray($query);
    }
    
    public function igToPromes($date_debut, $date_fin){
        $query = "SELECT id_ticket, REF_Service, typo_service, debut_incident, date_cloture_incident, IG, Commentaire
						FROM ticket_control
						WHERE date(debut_incident) >= '".$date_debut."'
						AND date(date_cloture_incident) = '".$date_fin."'
						AND etat not in (11)";
			
        return $this->fetchAllToArray($query);
    }
     
   

}

