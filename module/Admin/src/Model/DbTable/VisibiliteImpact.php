<?php

namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;
use Zend\Db\Sql\Select;

class VisibiliteImpact extends Base {

    protected $table = 'visibilite_impact';

    public function __construct(AdapterInterface $db) {
        $this->adapter = $db;
        $this->initialize();
    }

    public function find() {
        return $this->fetchAll(
                        $this->select(function(Select $select) {
                            $select->order('id_event DESC');
                        })
        );
    }
    
    public function findAll() {
        return $this->fetchAll(
                        $this->select(function(Select $select) {
                            $select->order('id_event DESC');
                        })
        );
    }
    
    
    
    public function findByEvent($event)
    {
        return $this->select([
            'event' => $event
        ]);
    }

}
