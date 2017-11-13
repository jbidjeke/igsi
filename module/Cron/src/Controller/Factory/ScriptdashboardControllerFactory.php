<?php
namespace Cron\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface; // <-- note the change!
use Zend\Db\Adapter\AdapterInterface;
use Zend\View\Renderer\PhpRenderer;



class ScriptdashboardControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    { 
        $config = $container->get('Config');
        return new $requestedName(
            $config,
            $container->get(PhpRenderer::class),
            $container->get(AdapterInterface::class), 
            $container->get(\Cron\Model\DbTable\IgApp::class),
            $container->get(\Cron\Model\DbTable\EsmsApp::class),
            $container->get(\Classes\Utile\CalculDate::class),
            $container->get(\Classes\Utile\IgDashboard::class),
            $container->get(\Classes\Utile\CalculEvolutionIg::class),
            $container->get(\Classes\Utile\Mailer::class)          
        );
    }
}

?>