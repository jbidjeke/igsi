<?php
namespace Application\Model\DbTable\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface; // <-- note the change!
use Zend\Db\Adapter\AdapterInterface;



class DbtableFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    { // Connecter à la base igsi
        $dependency = $container->get(AdapterInterface::class);
        return new $requestedName($dependency);
    }
}

