<?php

namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class MessageDashboard extends Base {

    protected $table = 'message_dashboard';

    public function __construct(AdapterInterface $db) {
        $this->adapter = $db;
        $this->initialize();
    }

    public function findAll() {
        return $this->getAdapter()->fetchAll('SELECT * FROM message_dashboard ORDER BY categorie_service,IG');
    }
    
    public function find($IG, $categorie_service) {
        return $this->getAdapter()->fetchAll("SELECT * FROM `message_dashboard` WHERE `IG` = $IG AND `categorie_service` LIKE '$categorie_service' ");
    }

    public function updateMessage($IG, $categorie_service, $message) {
        $sql = "SELECT * FROM `message_dashboard` WHERE `IG` = $IG AND `categorie_service` = '$categorie_service'";
        $result = $this->fetchAllToArray($sql);
        if (count($result))
            $sql = "UPDATE `message_dashboard` SET `message` = '$message' WHERE `IG` = " . $IG . " AND `categorie_service` = '" . $categorie_service . "'";
        else
            $sql = "INSERT INTO `message_dashboard` (`IG`, `categorie_service`, `message`) VALUES ($IG, '$categorie_service', '$message');";

        $this->doQuery($sql);
    }

}
