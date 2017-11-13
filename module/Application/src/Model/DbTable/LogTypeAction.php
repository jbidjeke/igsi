<?php
namespace Application\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class LogTypeAction extends Base
{
    protected $table = 'log_type_action';

    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db;
        $this->initialize();
         
    }

	public function findById($id)
	{
		return $this->fetchAll($this->select(['id_type_action' => $id]));
	}
	
	public function findByNom($nom)
	{
		return $this->fetchAll($this->select(['nom_type_action' => $nom]));
	}
	
	public function findOpen($param)
	{
		return $this->getAdapter()->fetchAll("SELECT *
											  FROM ticket_control tc,services s
											  WHERE tc.typo_service=s.Nom AND
											  cloture='NON' AND gele='NON' AND NOM LIKE '".$param."%'
											  AND tc.etat AND tc.etat not in (11)
		                                      ORDER BY id_ticket 
											  limit 30");
	
	}
	
	
	
	
	
}

