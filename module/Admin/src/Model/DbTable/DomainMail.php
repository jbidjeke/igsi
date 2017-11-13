<?php

namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class DomainMail extends Base {

    protected $table = 'domain_mail';

    public function __construct(AdapterInterface $db) {
        $this->adapter = $db;
        $this->initialize();
    }

    public function find() {
        return $this->getAdapter()->fetchAll('SELECT * 
								FROM domain_mail');
    }

}
