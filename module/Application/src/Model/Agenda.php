<?php 
namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;

class Agenda extends AbstractTableGateway{
 
    protected $_name = 'agenda';
    
    public function __construct($adapter)    
    {       
        $this->adapter = $adapter;    
    }
 
}
?>