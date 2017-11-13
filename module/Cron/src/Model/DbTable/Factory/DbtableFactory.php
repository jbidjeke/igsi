<?php
namespace Cron\Model\DbTable\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface; // <-- note the change!
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter;



class DbtableFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName == 'Cron\Model\DbTable\IgApp')
            $dependency = $container->get(AdapterInterface::class);
        
        if ($requestedName == 'Cron\Model\DbTable\EsmsApp'){
            $config = $container->get('Config');
            $dependency = new Adapter\Adapter($config['db_esms']);
        }
        
        return new $requestedName($dependency);
    }
}

