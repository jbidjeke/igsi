<?php
namespace Application\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class LogActionPage extends Base
{
    protected $table = 'log_action_page';

    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db;
        $this->initialize();
         
    }
			
	public function findById($id)
	{
		return $this->fetchAll($this->select(['id_action_page'=>$id]));
	}
	
	public function findByNom($nom)
	{
		return $this->fetchAll($this->select(['nom_action_page'=>$nom]));
	}	
}

