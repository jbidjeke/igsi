<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface; // <-- note the change!
use Zend\Db\Adapter\AdapterInterface;
use Classes\MyLibrary;
use Admin\Model\DbTable;


class AdminControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $formManager = $container->get('FormElementManager');
  
        return new $requestedName(
            $container->get(AdapterInterface::class), 
            $container->get(MyLibrary\MyLogApplicatif::class),
            $container->get(DbTable\HistoriqueModifications::class),
            $container->get(\Application\Model\DbTable\LogControllerPage::class),
            $container->get(\Application\Model\DbTable\LogActionPage::class),
            $container->get(\Application\Model\DbTable\LogTypeAction::class),
            $container->get(\Application\Model\DbTable\LogApplicatif::class),
            $container->get(DbTable\MessageDashboard::class),
            $container->get(DbTable\CategorieServices::class),
            $container->get(DbTable\Ponderations::class),
            $container->get(DbTable\PonderationsDynamique::class),
            $container->get(DbTable\TicketControl::class),
            
            $container->get(DbTable\EvolutionPrevu::class),
            $container->get(DbTable\EvolutionTicket::class),
            $container->get(DbTable\HistoriqueMail::class),
            $container->get(DbTable\HistoriqueSms::class),
            $container->get(DbTable\ControlGel::class),
            
            $formManager->get(\Admin\Form\AjouterCategorie::class),
            $formManager->get(\Calcul\Form\Calcul::class)
            
        );
    }
}

?>