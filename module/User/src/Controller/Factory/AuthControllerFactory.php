<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\AuthController;
use Zend\ServiceManager\Factory\FactoryInterface;
use User\Service\AuthManager;
use User\Service\UserManager;
use User\Form\LoginForm;

/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {   
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $authManager = $container->get(AuthManager::class);
        $authService = $container->get(\Zend\Authentication\AuthenticationService::class);
        $userManager = $container->get(UserManager::class);
        
        $formManager = $container->get('FormElementManager');
        $loginForm = $formManager->get(LoginForm::class);
        $mailer = $container->get(\Classes\Utile\Mailer::class); 
        
        return new AuthController($entityManager, $authManager, $authService, $userManager, $loginForm, $mailer);
    }
}
