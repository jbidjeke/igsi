<?php

namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class Message extends Base {

    protected $table = 'message';

    public function __construct(AdapterInterface $db) {
        $this->adapter = $db;
        $this->initialize();
    }

    public function findMessage($IG_debut, $IG_encours, $categorie_service, $id_ticket) {
        //echo "TICKET: ".$id_ticket."---IG_debut: ".$IG_debut."---IG_encours: ".$IG_encours."<br>";
        return $this->getAdapter()->fetchAll("SELECT md.IG,
            md.categorie_service
            FROM message_dashboard md
            LEFT OUTER JOIN message m ON (md.IG = m.IG 
            AND md.categorie_service = m.categorie_service 
            AND m.id_ticket = '" . $id_ticket . "')
            WHERE (md.IG >= " . $IG_debut . " AND md.IG <= " . $IG_encours . ") 
            AND md.categorie_service = '" . $categorie_service . "'
            AND m.IG IS NULL");
    }

}
