<?php
namespace Admin\Model\DbTable\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface; // <-- note the change!
use Zend\Db\Adapter;
use Zend\View\Renderer\PhpRenderer;

/* 
 * La classe fabrique de model. 
 * Elle permet à chaque model de se connecter à une base de donnée grâce aux injections de dependence
 */
class DbtableFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {   
        if ($requestedName == '\Admin\Model\DbTable\AssetQuery')
        {
            $config = $container->get('Config');
            $dependency = new Adapter\Adapter($config['CTT_Center_SI']);
        }
        else{// Connexion à la base en fonction d'un profil ou d'une table spécifique
            $view = $container->get(PhpRenderer::class);
            $user = $view->currentUser();
            $role = $user->getRoles()->current();
            $nameRole = $role->getName();
            if (in_array($nameRole, ['Administrateur-bur', 'Exploitant-bur']) || in_array($requestedName,['Admin\Model\DbTable\Fap','Admin\Model\DbTable\TicketFap'])) 
            {   //  Connecter base igbur
                $config = $container->get('Config');
                $dependency = new Adapter\Adapter($config['db_igBureautique']);
            }
            else // Connecter base igsi
                $dependency = $container->get(Adapter\AdapterInterface::class);
        }
        
        return new $requestedName($dependency);
    }
}

