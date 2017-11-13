<?php

namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class EvolutionPrevu extends Base
{
    protected $table = 'evolution_prevu';

    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db;
        $this->initialize();
         
    }

}

