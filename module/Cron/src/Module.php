<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Cron;


use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;


class Module implements ConfigProviderInterface, ConsoleUsageProviderInterface
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    
    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\InsertController::class => Controller\Factory\InsertControllerFactory::class,
                Controller\DeleteController::class => Controller\Factory\DeleteControllerFactory::class,
                Controller\ExtractController::class => Controller\Factory\ExtractControllerFactory::class,    
                Controller\ScriptdashboardController::class => Controller\Factory\ScriptdashboardControllerFactory::class,
            ],
        ];
    }
    
    public function getServiceConfig()
    {
        return [
            'factories' => [ // registering a factories instance  
                Model\DbTable\EsmsApp::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\IgApp::class => Model\DbTable\Factory\DbtableFactory::class,
                \Classes\Utile\CalculDate::class => \Classes\Utile\Factory\CalculDateFactory::class,
                \Classes\Utile\Mailer::class => \Classes\Utile\Factory\MailerFactory::class
             ],
        ];
    }
    
    public function getConsoleUsage(Console $console)
    {
        return [
            // Describe available commands
            'prod_insert_fich_esms index' => 'Insert Esms app from datain/esms_app.txt or datain/esms_app_bckup.txt to database igsi',
            /* Generation de fichier csv à extraire */
            'extract hebdo_esms_ec' => 'Generation hebdomadaire de fichier esms_ec a extraire',
            'extract esms_encours' => 'Generation de fichier esms en cours',
            'extract hebdo_esms' => 'Generation csv esms hebdo',
            'extract hebdo_ig_ec' => 'Generation csv ig ec hebdo',
            'extract hebdo_ig' => 'Generation csv ig hebdo',
            'extract sixmonths'=> 'Generation csv sixmonths',
            'extract ig_to_promes'=> 'Generation csv pour promes',
            /*insert*/
            'prod_insert_fich_igsi_esms_app' => 'Insertion app igsi esms',
            /* mail*/
            'scriptdashboard ig_hebdo' =>'Envoi mail hebdo ig et copie de fichier',
            'scriptdashboard ig_mensuel' =>'Envoi mail mensuel ig et copie de fichier',
            'scriptdashboard esms_ig_hebdo' =>'Envoi mail hebdo esms ig et copie de fichier',
            'scriptdashboard esms_ig_mensuel' => 'Envoi mail mensuel esms ig et copie de fichier',
            'esms_purge_histo_tables' => 'purge la table esms_histo_noc' 
            
            
        ];
    }
}
