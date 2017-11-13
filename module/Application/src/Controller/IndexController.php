<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;


use Zend\View\Model\ViewModel;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Session\Container;
use Application\Model\DbTable\LogApplicatif;
use Classes\MyLibrary\MyLogApplicatif;
use Application\Controller\BaseController;

class IndexController extends BaseController
{    
   // use \Classes\MyTrait\Mytrace;  //Pour réutilisation de methodes logApplication et historisation de la librairie devoteamclasses dans le dossier vendor
    
    protected $db, $logApplicatif;
    protected $profil = 'guest';

    
    public function dispatch(
        RequestInterface $request,
        ResponseInterface $response = null
        )
    {
    
        $this->container = new Container('initialized');
    
        //$this->layout()->setTemplate('layout/layout-bootstrap');
        $this->layout()->setVariable('page', 'accueil');
        /*
         $this->profil = $this->container->profil;
         $this->layout()->setVariable('profil', $this->profil);
         $this->layout()->setVariable('id_user', $this->container->id_user);
         */
        $this->actionName = $this->params('action');
        $this->layout()->setVariable('nom_action', $this->actionName);
        $this->controllerName = $this->params('controller');
        $this->layout()->setVariable('nom_controller', $this->controllerName);
        parent::dispatch($request, $response);
    
    }
    
    public function __construct(AdapterInterface $db, LogApplicatif $logApplicatif, MyLogApplicatif  $log)
    {
        
        $this->db = $db;
        $this->log = $log;
        $this->logApplicatif = $logApplicatif;
        
    }
    
    
    public function indexAction()
    { 
        $this->layout()->setVariable('subTitle', 'Accueil');
        $id_log = $this->params()->fromQuery('id_log', '');
        if (isset($id_log))
        {
            try
            { 
                $find_val = $this->logApplicatif->findIPByLog($id_log);

                $ip_user = $_SERVER['REMOTE_ADDR'];
                
                foreach($find_val as $val)
                    $ip_user = $val['ip_user'];
                  
                $this->container->ipUser = $ip_user; 
            }
            catch (\Exception $e)
            {
            }
        }
        
        /******** LOG APPLICATIF ********/
        $this->logApplication("affichage", 0, "Affichage de la page d'accueil");
        /****** FIN LOG APPLICATIF ******/
        
        return new ViewModel();
    }
}
