<?php
namespace Admin\Model\DbTable\Ig;

use Application\Model\DbTable\Base;


class OthersIg extends Base
{
    protected $table = "OthersIg", $adapter;
    
    public function __construct($dbigBureautique)
    {
        $this->adapter = $dbigBureautique;
        $this->initialize();
         
    }
    
	public function dashboardIgBureautique()
	{
	    $select = "SELECT TypeIG,ticket_control.id_ticket, ticket_control.typo_service, ticket_control.debut_incident, ticket_control.categorie_service,
									ticket_control.cloture,  ticket_control.nom_admin, ticket_control.sous_systeme, ticket_control.msg_pronet,
									ticket_control.gele, evolution_ticket.duree_incident_creation,  evolution_ticket.IG_debut, evolution_ticket.IG_encours,
									evolution_ticket.duree_incident, evolution_ticket.date_prochaine_incident, evolution_ticket.IG_prochaine
									FROM ticket_control,  evolution_ticket
									WHERE ticket_control.id_ticket=evolution_ticket.id_ticket AND ticket_control.cloture='NON'
									ORDER BY ticket_control.categorie_service IN('Best Effort'),
												ticket_control.categorie_service DESC,
												ticket_control.id_ticket DESC";

		return $this->fetchAll($select);
		
	}
}