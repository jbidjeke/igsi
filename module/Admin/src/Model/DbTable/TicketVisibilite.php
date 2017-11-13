<?php
namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;


class TicketVisibilite extends Base
{
    protected $table = 'ticket_visibilite';
    
    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db;
        $this->initialize();
         
    }
    
	public function findByTicket($ticket)
	{
		return $this->select(['id_ticket'=>$ticket]);
	}
}