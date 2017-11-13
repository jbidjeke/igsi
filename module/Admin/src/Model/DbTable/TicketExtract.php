<?php

namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class TicketExtract extends Base {

    protected $table = 'ticket_control';

    public function __construct(AdapterInterface $db) {
        $this->adapter = $db;
        $this->initialize();
    }

    public function findExtractIGHebdo($date_debut, $date_fin) {
        return $this->getAdapter()->fetchAll("SELECT ticket_control.id_ticket, 
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
											AND ticket_control.debut_incident >= '" . $date_debut . "' 
											AND ticket_control.debut_incident <= '" . $date_fin . "'
		                                    AND ticket_control.etat not in (10,11)
											ORDER BY ticket_control.categorie_service IN('Best Effort'),
												     ticket_control.categorie_service DESC, 
												     ticket_control.id_ticket DESC
			");
    }

    public function findExtractESMSHebdo($date_debut, $date_fin) {
        return $this->fetchAll("SELECT vefd.prefix_ticket, 
									 vefd.id_ticket, 
									 vefd.nature_com,
									 vefd.specifique_com, 
									 vefd.date_envoi_sms, 
									 vefd.domaine, 
									 vefd.ref_appli, 
									 vefd.nom_appli, 
									 vefd.ig, 
									 vefd.date_debut_inc, 
									 vefd.date_fin_inc, 
									 vefd.description 
							  FROM view_esms_si_dashboard AS vefd
							  WHERE vefd.date_debut_inc >= '" . $date_debut . "' 
							  AND vefd.date_debut_inc <= '" . $date_fin . "' 
							  ORDER BY vefd.nom_appli ASC
			");
    }

}
