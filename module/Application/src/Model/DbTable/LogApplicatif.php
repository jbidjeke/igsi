<?php
namespace Application\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;

class LogApplicatif extends Base
{
    protected $table = 'log_applicatif';
    
    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db; 
        $this->initialize();
       
    }
	
	public function findIPByLog($id_log)
	{
	    $select = $this->sql->select();
	    $select->where(['id_log' => $id_log]);	    
		return $this->fetchAll($select);
	}
	
	public function afficheAll($limit,
							   $insertion_bdd)
	{
		return $this->fetchAll("SELECT la.id_log AS id_log,
									   la.ip_user AS ip_user,
									   la.date_log AS date_log,
									   la.description AS description,
									   lcp.nom_controller_page AS nom_controller,
									   lap.nom_action_page AS nom_action,
									   lta.nom_type_action AS nom_type_action,
									   u.login AS login
								FROM log_applicatif la,
									 log_controller_page lcp,
									 log_action_page lap,
									 log_type_action lta,
									 users u
								WHERE la.id_controller_page = lcp.id_controller_page 
								AND	  la.id_action_page = lap.id_action_page 
								AND	  la.id_type_action = lta.id_type_action
								AND	  la.id_user = u.id_user
								AND	  lta.insertion_bdd = ".$insertion_bdd." 			
								ORDER BY la.date_log DESC
								limit ".$limit);
	}
	
	public function afficheAllFiltre($limit,
							   		 $insertion_bdd,
							   		 $id_log,
							   		 $ip_user,
							   		 $date_log_sup,
							   		 $date_log_inf,		
							   		 $controller,		
							   		 $action,	
							   		 $type_action,
							   		 $login,
							   		 $tri)
	{
		if(isset($tri))
		{	
			switch ($tri) 
			{
				case "id_log":
					$order = "la.id_log ASC ";
					break;
				case "id_log_desc":
					$order = "la.id_log DESC ";
					break;	
				case "ip":
					$order = "la.ip_user ASC ";
					break;
				case "ip_desc":
					$order = "la.ip_user DESC ";
					break;	
				case "utilisateur":
					$order = "u.login ASC ";
					break;
				case "utilisateur_desc":
					$order = "u.login DESC ";
					break;		
				case "date_log":
					$order = "la.date_log ASC ";
					break;
				case "date_log_desc":
					$order = "la.date_log DESC ";
					break;		
				case "controller":
					$order = "lcp.nom_controller_page ASC ";
					break;
				case "controller_desc":
					$order = "lcp.nom_controller_page DESC ";
					break;		
				case "action":
					$order = "lap.nom_action_page ASC ";
					break;
				case "action_desc":
					$order = "lap.nom_action_page DESC ";
					break;		
				case "type_action":
					$order = "lta.nom_type_action ASC ";
					break;
				case "type_action_desc":
					$order = "lta.nom_type_action DESC ";
					break;		
				case "description":
					$order = "la.description ASC ";
					break;
				case "description_desc":
					$order = "la.description DESC ";
					break;		
			}
		}
		else
			$order = "la.date_log DESC ";		
		
		$andidlog = "";
		if (isset($id_log) && $id_log != "")
			$andidlog = " AND la.id_log = '".$id_log."' ";
			
		$andipuser = "";
		if (isset($ip_user) && $ip_user != "")
			$andipuser = " AND la.ip_user LIKE '%".$ip_user."%' ";
			
		$anddatesup = "";
		if (isset($date_log_sup) && $date_log_sup != "")
			$anddatesup = " AND la.date_log >= '".trim($date_log_sup).":00"."' ";
			
		$anddateinf = "";
		if (isset($date_log_inf) && $date_log_inf != "")
			$anddateinf = " AND la.date_log <= '".trim($date_log_inf).":00"."' ";
			
		$andcontroller = "";
		if (count($controller) != 0)
		{
			$andcontroller = " AND (";
			$i = 0;
			foreach ($controller as $value)
			{
				if ($i != 0)
					$andcontroller .= " OR ";
				$andcontroller .= " lcp.nom_controller_page LIKE '%".$value."%' ";	
				$i++;
			}
			$andcontroller .= ")";
		}
		
		$andaction = "";
		if (count($action) != 0)
		{
			$andaction = " AND (";
			$i = 0;
			foreach ($action as $value)
			{
				if ($i != 0)
					$andaction .= " OR ";
				$andaction .= " lap.nom_action_page LIKE '%".$value."%' ";	
				$i++;
			}
			$andaction .= ")";
		}
		
		$andtypeaction = "";
		if (count($type_action) != 0)
		{
			$andtypeaction = " AND (";
			$i = 0;
			foreach ($type_action as $value)
			{
				if ($i != 0)
					$andtypeaction .= " OR ";
				$andtypeaction .= " lta.nom_type_action LIKE '%".$value."%' ";	
				$i++;
			}
			$andtypeaction .= ")";
		}
		$andlogin = "";		
		if (isset($login) && $login != "")
			$andlogin = " AND u.login LIKE '%".$login."%' ";
		$andinsertionbdd = "";		
		if ($insertion_bdd == 1)
			$andinsertionbdd = " AND lta.insertion_bdd = ".$insertion_bdd." ";
		$closeWhere = $andidlog.$andipuser.$anddatesup.$anddateinf.$andcontroller.$andaction.$andtypeaction.$andlogin.$andinsertionbdd;
		
		return $this->fetchAll("SELECT la.id_log AS id_log,
									   la.ip_user AS ip_user,
									   la.date_log AS date_log,
									   la.description AS description,
									   lcp.nom_controller_page AS nom_controller,
									   lap.nom_action_page AS nom_action,
									   lta.nom_type_action AS nom_type_action,
									   u.login AS login
								FROM log_applicatif la,
									 log_controller_page lcp,
									 log_action_page lap,
									 log_type_action lta,
									 users u
								WHERE la.id_controller_page = lcp.id_controller_page 
								AND	  la.id_action_page = lap.id_action_page 
								AND	  la.id_type_action = lta.id_type_action
								AND	  la.id_user = u.id_user  
								".$closeWhere."		
								ORDER BY ".$order." 
								limit ".$limit);
	}
}

