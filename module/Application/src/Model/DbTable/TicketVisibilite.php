<?php
namespace Application\Model\DbTable;

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
		return $this->fetchAll(
						$this->select()
								->where('id_ticket = :ticket')
								->bind(array(':ticket'=>$ticket))
								);
	}
}