<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface; // <-- note the change!
use Zend\Db\Adapter\AdapterInterface;
use Classes\Utile\CalculEvolutionIg;
use Classes\Utile\CalculEvolutionIgBur;
use Classes\Utile\IgDashboard;
use Classes\MyLibrary\MyLogApplicatif;
use Admin\Model\DbTable\TicketControl;
use Admin\Model\DbTable\MessageDashboard;
use Admin\Model\DbTable\HistoriqueModifications;
use Admin\Model\DbTable\Message;
use Admin\Model\DbTable\Ig\OthersIg;
use Admin\Form\Pilote;


class DashboardControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $formManager = $container->get('FormElementManager');
        if ($requestedName == "Admin\Controller\Dashboard\DashboardController")
			return new $requestedName(
				$container->get(AdapterInterface::class),
				$container->get(MyLogApplicatif::class),
				$container->get(HistoriqueModifications::class),
				$container->get(CalculEvolutionIg::class),
				$container->get(\Classes\Utile\CalculDate::class),
				$container->get(TicketControl::class),
				$container->get(MessageDashboard::class),
				$container->get(Message::class),
				$container->get(OthersIg::class),
				$formManager->get(Pilote::class),
				$container->get(IgDashboard::class)
			);
		else
			return new $requestedName(
				$container->get(AdapterInterface::class),
				$container->get(MyLogApplicatif::class),
				$container->get(HistoriqueModifications::class),
				$container->get(CalculEvolutionIgBur::class),
				$container->get(\Classes\Utile\CalculDate::class),
				$container->get(TicketControl::class),
				$container->get(MessageDashboard::class),
				$container->get(Message::class),
				$container->get(OthersIg::class),
				$formManager->get(Pilote::class),
				$container->get(IgDashboard::class)
			);

			
    }
}

?>