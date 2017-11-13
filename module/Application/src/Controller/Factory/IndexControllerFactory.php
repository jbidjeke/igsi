<?php
namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface; // <-- note the change!
use Zend\Db\Adapter\AdapterInterface;
use Classes\MyLibrary\MyLogApplicatif;


class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new $requestedName(
            $container->get(AdapterInterface::class), 
            $container->get(\Application\Model\DbTable\LogApplicatif::class),
            $container->get(MyLogApplicatif::class)
            
        );
    }
}

?>