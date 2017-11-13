<?php
namespace Admin\Model\DbTable;

use Zend\Db\Adapter\AdapterInterface;
use Application\Model\DbTable\Base;

class HistoriqueMail extends Base
{
    protected $table = 'historique_mail';

    public function __construct(AdapterInterface $db)
    {
        $this->adapter = $db;
        $this->initialize();
         
    }

}

