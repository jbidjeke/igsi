<?php
namespace Admin\Model\DbTable\Ig\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface; // <-- note the change!
use Zend\Db\Adapter;




class OthersIgFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $adapter_igSi = new Adapter\Adapter($config['db_igsi']);
        return new $requestedName($adapter_igSi);
    }
}
