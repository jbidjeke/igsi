<?php
namespace Admin\Model\DbTable\AbstractFacotry;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface; // <-- note the change!
use Zend\Db\Adapter\AdapterInterface;

class DbtableAbstractFactory implements AbstractFactoryInterface
{
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return class_exists($requestedName);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dependency = $container->get(AdapterInterface::class);
        return new $requestedName($dependency);
    }
    
}

