<?php

namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;
use Zend\Db\Sql\Select;

/*
 * Valeur possible du champ "ticket_control_temp.etat"
 * Etat Demande Création Ig: 10 ->annulation creation, 11 ->soumission création, 12->validation création; Etat Demande gel Ig: 20->annulation, 21->soumission,22->validation;Etat Demande degel Ig: 30->annulation, 31->soumission,32->validation;Etat Demande Cloture Ig: 40->annulation, 41->soumission,42->validation;Etat Demande Modif Ig: 50->annulation, 51->soumission,52->validation;
 *
 */

class TicketControlTemp extends Base {

    protected $table = 'ticket_control_temp';

    public function __construct(AdapterInterface $db) {
        $this->adapter = $db;
        $this->initialize();
    }

    public function findByTicket($ticket) {
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

}
