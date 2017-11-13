<?php

namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class BusyHour extends Base {

    protected $table = 'busy_hour';

    public function __construct(AdapterInterface $db) {
        $this->adapter = $db;
        $this->initialize();
    }

    public function find() {
        return $this->fetchAll(
                        $this->select()
        );
    }

}
