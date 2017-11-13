<?php
namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class HistoriqueModifications extends Base
{
    protected $table = 'historique_modifications';
    
    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db;
        $this->initialize();
         
    }
    
}

