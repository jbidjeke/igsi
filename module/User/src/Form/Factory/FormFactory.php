<?php
namespace User\Form\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface; // <-- note the change!
use Zend\View\Renderer\PhpRenderer;

class FormFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {      
        return new $requestedName(
            $container->get(PhpRenderer::class)// $view
        );
    }
}

?>