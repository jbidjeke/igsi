<?php
namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class Fap extends Base
{

    protected $table = 'fap';

    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db;
        $this->initialize();
    }
    
    public function find($v)
    {
        return $this->select([
            'Population' => $v
        ]);
    }

    public function findAll()
    {
        return $this->select();
    }

    public function findAllCompletion()
    {
        return $this->getAdapter()->fetchAll('SELECT Population as name,Population as id
								FROM fap
								ORDER BY Population
								');
    }
}

