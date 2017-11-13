<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\Session\Validator;
/*use Zend\View\HelperPluginManager;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Resource\GenericResource;*/
use User\Controller\AuthController;
use User\Service\AuthManager;

class Module
{
    /**
     * This method returns the path to module.config.php file.
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    
    /**
     * This method is called once the MVC bootstrapping is complete and allows
     * to register event listeners. 
     */
    public function onBootstrap(MvcEvent $event)
    {

        if (php_sapi_name() !== 'cli'){
            // Get event manager.
            $eventManager = $event->getApplication()->getEventManager();
            $sharedEventManager = $eventManager->getSharedManager();
            // Register the event listener method.
            $sharedEventManager->attach(AbstractActionController::class,
                MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
            
            $this->bootstrapSession($event);
        }
    }
    
    public function getFormElementConfig()
    {
        return [
            'factories' => [
                Form\LoginForm::class => Form\Factory\FormFactory::class
            ],
        ];
    }
    
    public function bootstrapSession($e)
    {
        /*$session = $e->getApplication()
        ->getServiceManager()
        ->get(SessionManager::class);
        
        if ($session->sessionExists())
            $session->start();*/
    
        $container = new Container('initialized');
    
        if (isset($container->init)) {
            return;
        }
    
        $serviceManager = $e->getApplication()->getServiceManager();
        $request        = $serviceManager->get('Request');
    
        //$session->regenerateId(true);
        $container->init          = 1;
        $container->remoteAddr    = $request->getServer()->get('REMOTE_ADDR');
        $container->httpUserAgent = $request->getServer()->get('HTTP_USER_AGENT');
    
        $config = $serviceManager->get('Config');
        if (! isset($config['session'])) {
            return;
        }
    
        $sessionConfig = $config['session'];
    
        if (! isset($sessionConfig['validators'])) {
            return;
        }
    
        /*$chain   = $session->getValidatorChain();
    
        foreach ($sessionConfig['validators'] as $validator) {
            switch ($validator) {
                case Validator\HttpUserAgent::class:
                    //$validator = new $validator($container->httpUserAgent);
                    break;
                case Validator\RemoteAddr::class:
                    //$validator  = new $validator($container->remoteAddr);
                    break;
                default:
                    $validator = new $validator();
            }
    
            $chain->attach('session.validate', array($validator, 'isValid'));
        }*/
    }
    
    /**
     * Event listener method for the 'Dispatch' event. We listen to the Dispatch
     * event to call the access filter. The access filter allows to determine if
     * the current visitor is allowed to see the page or not. If he/she
     * is not authorized and is not allowed to see the page, we redirect the user 
     * to the login page.
     */
    public function onDispatch(MvcEvent $event)
    {
        // Get controller and action to which the HTTP request was dispatched.
        $controller = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $actionName = $event->getRouteMatch()->getParam('action', null);
        
        // Convert dash-style action name to camel-case.
        $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));
        
        // Get the instance of AuthManager service.
        $sm = $event->getApplication()->getServiceManager();
        $config = $sm->get('Config');
        $authManager = $sm->get(AuthManager::class);
        
        
        // Execute the access filter on every controller except AuthController
        // (to avoid infinite redirect).
        if ($controllerName != AuthController::class &&  !in_array($controllerName, $config['except-controller']))
        {  
            $result = $authManager->filterAccess($controllerName, $actionName);
            
            if ($result==AuthManager::AUTH_REQUIRED) {
                // Remember the URL of the page the user tried to access. We will
                // redirect the user to that URL after successful login.
                $uri = $event->getApplication()->getRequest()->getUri();
                // Make the URL relative (remove scheme, user info, host name and port)
                // to avoid redirecting to other domain by a malicious user.
                $uri->setScheme(null)
                    ->setHost(null)
                    ->setPort(null)
                    ->setUserInfo(null);
                $redirectUrl = $uri->toString();

                // Redirect the user to the "Login" page.
                return $controller->redirect()->toRoute('login', [], 
                        ['query'=>['redirectUrl'=>$redirectUrl]]);
            }
            else if ($result==AuthManager::ACCESS_DENIED) {
                // Redirect the user to the "Not Authorized" page.
                return $controller->redirect()->toRoute('not-authorized');
            }
        }
    }
    
    
    /*public function getViewHelperConfig()
     {
     return [
     'factories' => [
     // This will overwrite the native navigation helper
     'navigation' => function(HelperPluginManager $pm) {
     // Setup ACL:
     $acl = new Acl();
     $acl->addRole(new GenericRole('user.pilote'));
     $acl->addRole(new GenericRole('user.manage'));
     $acl->addResource(new GenericResource('mvc:admin.account'));
     $acl->addResource(new GenericResource('mvc:pilote.account'));
     $acl->allow('user.pilote', 'mvc:pilote.account');
     $acl->allow('user.manage', null);
     
     // Get an instance of the proxy helper
     $navigation = $pm->get('Zend\View\Helper\Navigation');
     
     // Store ACL and role in the proxy helper:
     $navigation->setAcl($acl);
     $navigation->setRole('user.manage');
     
     // Return the new navigation helper instance
     return $navigation;
     }
     ]
     ];
     }*/
    
}
