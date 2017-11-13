<?php
namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;
use Zend\Db\Sql\Select;

class TicketFap extends Base
{

    protected $table = 'ticket_fap';

    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db;
        $this->initialize();
    }

    public function findByTicket($ticket)
    {
        return $this->fetchAll($this->select(function (Select $select) use($ticket) {
            $predicate = [
                'id_ticket' => $ticket
            ];
            $select->where($predicate);
        }));
    }
}

