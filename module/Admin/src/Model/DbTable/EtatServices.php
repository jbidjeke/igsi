<?php

namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;
use Zend\Db\Sql\Select;

class EtatServices extends Base {

    protected $table = 'etat_services';

    public function __construct(AdapterInterface $db) {
        $this->adapter = $db;
        $this->initialize();
    }

    public function find() {
        return $this->fetchAll(
                        $this->select(function(Select $select) {
                            $select->order('id_etat');
                        }));
    }

}
