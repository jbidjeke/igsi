<?php
namespace Application\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class Pfs extends Base
{
    protected $table = 'pfs';

    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db;
        $this->initialize();
         
    }

	public function find()
	{
		return $this->fetchAll(
						$this->select()
								->order('pfs')
								);
	}
}

