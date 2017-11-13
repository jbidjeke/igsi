<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ServiceManager\Factory\InvokableFactory;
use Classes\Utile;
use Classes\Validate;


class Module implements ConfigProviderInterface
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
                Controller\AdminController::class => Controller\Factory\AdminControllerFactory::class,
                Controller\DashboardController::class => Controller\Factory\DashboardControllerFactory::class,
				Controller\DashboardburController::class => Controller\Factory\DashboardControllerFactory::class,
            ],
        ];
    }
    
    public function getServiceConfig()
    {
        return [
            'factories' => [ // registering a factories instance  
                Model\DbTable\HistoriqueModifications::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\EvolutionTicket::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\NumServicesImpactes::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\PonderationsDynamique::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\Ponderations::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\DureeImpact::class => Model\DbTable\Factory\DbtableFactory::class, 
                Model\DbTable\TicketExtract::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\MessageDashboard::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\Message::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\Services::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\CategorieServices::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\TicketControl::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\TicketControlTemp::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\EtatServices::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\VisibiliteImpact::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\BusyHour::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\DomainMail::class => Model\DbTable\Factory\DbtableFactory::class,
                
                Model\DbTable\EvolutionPrevu::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\EvolutionTicket::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\HistoriqueMail::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\HistoriqueSms::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\ControlGel::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\AssetQuery::class => Model\DbTable\Factory\DbtableFactory::class,
                
                Model\DbTable\TicketVisibilite::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\TicketFap::class => Model\DbTable\Factory\DbtableFactory::class,
                Model\DbTable\Fap::class => Model\DbTable\Factory\DbtableFactory::class,
                
                
                Model\DbTable\Ig\OthersIg::class => Model\DbTable\Ig\Factory\OthersIgFactory::class,
                

                Utile\IgDashboard::class => Utile\Factory\IgDashboardFactory::class,
                Utile\ExtractCsvHebdo::class => Utile\Factory\ExtractCsvHebdoFactory::class,
                Utile\IgAction::class => Utile\Factory\IgActionFactory::class,
                Validate\ValidateService::class => Validate\Factory\ValidateServiceFactory::class,
            ],
            /*'abstract_factories' => [ // registering a factories instance
                Model\DbTable\HistoriqueModifications::class => Model\DbTable\AbstractFacotry\DbtableAbstractFactory::class,
            ],*/
        ];
    }
    
    
    public function getFormElementConfig()
    {
        return [
            'aliases' => [
                'categorie' => Form\AjouterCategorie::class,
            ],
            'factories' => [
                Form\AjouterCategorie::class => InvokableFactory::class,
                Form\Modifier::class => Form\Factory\FormFactory::class,
            ],
        ];
    }
}
