<?php
namespace Application\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class LogControllerPage extends Base
{
    protected $table = 'log_controller_page';

    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db;
        $this->initialize();
         
    }
			
	public function findById($id)
	{
		return $this->fetchAll($this->select(['id_controller_page'=>$id]));
	}
	
	public function findByNom($nom)
	{
		return $this->fetchAll($this->select(['nom_controller_page'=>$nom]));
	}	
}

