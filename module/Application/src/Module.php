<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Mail;
use Classes\MyLibrary;

class Module implements ConfigProviderInterface
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    
    /**
     * This method is called once the MVC bootstrapping is complete and allows
     * to set Locale.
     */
    public function onBootstrap(MvcEvent $event)
    {
        $container = $event->getApplication()->getServiceManager();
        $locale = $container->get('Zend\I18n\Translator\TranslatorInterface');
        $config = $container->get('Config');
        $locale->setLocale($config["locale"]);
    }
    
    
    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
                Controller\IgbureautiqueController::class=> Controller\Factory\IgbureautiqueControllerFactory::class,
            ],
        ];
    }
    
  
    /*public function getControllerPluginConfig()
    {
        return [
            'factories' => [
                'baseUrl' => \Classes\Plugin\Factory\BaseUrlPluginFactory::class,
            ],
        ];
    }*/
    
    public function getServiceConfig()
    {
        return [
            'factories' => [ // registering a factories instance 
               MyLibrary\MyLogApplicatif::class =>MyLibrary\Factory\MyLibraryFactory::class,
               Model\DbTable\LogApplicatif::class => Model\DbTable\Factory\DbtableFactory::class,
               Model\DbTable\LogControllerPage::class => Model\DbTable\Factory\DbtableFactory::class,
               Model\DbTable\LogActionPage::class => Model\DbTable\Factory\DbtableFactory::class,
               Model\DbTable\LogTypeAction::class => Model\DbTable\Factory\DbtableFactory::class,
                
               Mail\Message::class => InvokableFactory::class,
               Mail\Transport\Smtp::class => InvokableFactory::class,

             ],
           ];
    }
}
