<?php

namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class CategorieServices extends Base {

    protected $table = 'categorie_services';

    public function __construct(AdapterInterface $db) {
        $this->adapter = $db;
        $this->initialize();
    }

    public function selectCS() {
        return $this->fetchAll('SELECT * FROM categorie_services ORDER BY id');
    }

}
