<?php
namespace Cron\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface; // <-- note the change!
use Zend\Db\Adapter\AdapterInterface;



class InsertControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    { 
       $config = $container->get('Config');
        return new $requestedName(
            $config,
            $container->get(AdapterInterface::class), 
            $container->get(\Cron\Model\DbTable\EsmsApp::class),
            $container->get(\Cron\Model\DbTable\IgApp::class)
            
        );
    }
}

?>