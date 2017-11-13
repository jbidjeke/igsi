<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
//use Zend\View\Model\ViewModel;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

class ErrorController extends AbstractActionController
{
    public function dispatch(
        RequestInterface $request,
        ResponseInterface $response = null
        )
    {
        /*
        if (Zend_Auth::getInstance ()->hasIdentity ())
        {
            $get_identity = Zend_Auth::getInstance ()->getIdentity ();
            $this->view->profil = $get_identity->profil;
        }
        else
            $this->view->profil = "guest";
        */
        $this->layout()->setVariable('page', 'erreur');
        $this->layout()->setVariable('title', 'Erreur');
        
        /*$renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle()->prepend('un exemple depuis le controller');*/
        
        //$this->layout()->setVariable('base_url_public', 'Erreur');
        
        $actionName = $this->params('action');
        $this->layout()->setVariable('nom_action', $actionName);
        
        $controllerName = $this->params('controller');
        $this->layout()->setVariable('nom_controller', $controllerName);
        
        parent::dispatch($request, $response);
    }
}
