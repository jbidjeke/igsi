<?php
namespace Application\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class SessionRegister extends Base
{
    protected $table = 'session_register';

    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db;
        $this->initialize();
         
    }

	public function insertSessionReg($session_id,$session_mail){
		$cur_date = date("Y-m-d H:i:s");
		$data = array('id_session' => $session_id,
	    				  'session_mail' => $session_mail,
						  'session_debut' => $cur_date
	    				  );
		return $this->insert($data);
	}	
	public function deleteSessionReg($session_id){	
		return $this->delete('id_session="'.$session_id.'"');
	}
	
	public function deleteOldSessionReg($session_mail){	
		
		$date_suppression = date("Y-m-d H:i:s", strtotime('-8 hour'));
		$data = array('session_mail = ?'=> $session_mail ,'session_debut <= ? '=> $date_suppression);
		   	
		return $this->delete($data);
	}
}

