<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\Session\Container;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Paginator\Adapter;
use Zend\Paginator\Paginator;
use Classes\Utile\CalculDate;
use Classes\Utile\CalculEvolutionIg;
use Classes\MyLibrary\MyLogApplicatif;
use Classes\Utile\IgDashboard;
use Admin\Model\DbTable;
use Admin\Model\DbTable\Ig\OthersIg;
use Admin\Form\Pilote;
use Application\Controller\BaseController;


class DashboardController extends BaseController
{
    // use \Classes\MyTrait\Mytrace; //Pour réutilisation de methodes logApplication et historisation de la librairie devoteamclasses dans le dossier vendor
    protected $db, $calculEvolutionIg, $calculDate, $ticketControl, $messageDashboard, $message, $othersIg, $pilote, $igDashboard;

    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        $this->container = new Container('initialized');

        $this->layout()->setVariable('page', 'dashboard');

        $this->actionName = $this->params('action');
        $this->layout()->setVariable('nom_action', $this->actionName);
        $this->controllerName = $this->params('controller');
        $this->layout()->setVariable('nom_controller', $this->controllerName);
        
        parent::dispatch($request, $response);
    }

    public function __construct(AdapterInterface $db, MyLogApplicatif $log, DbTable\HistoriqueModifications $histo, CalculEvolutionIg $calculEvolutionIg, CalculDate $calculDate, DbTable\TicketControl $ticketControl, DbTable\MessageDashboard $messageDashboard, DbTable\Message $message, OthersIg $othersIg, Pilote $pilote, IgDashboard $igDashboard)
    {
        $this->db = $db;
        $this->log = $log;
        $this->histo = $histo;
        $this->calculEvolutionIg = $calculEvolutionIg;
        $this->calculDate = $calculDate;
        $this->ticketControl = $ticketControl;
        $this->pilote = $pilote;
        $this->messageDashboard = $messageDashboard;
        $this->message = $message;
        $this->othersIg = $othersIg;
        $this->igDashboard = $igDashboard;
    }

    public function indexAction()
    {
        $this->layout()->setVariable('subTitle', 'Dashboard SI');
        
        /**
         * ****** LOG APPLICATIF *******
         */
        $this->logApplication("affichage", 0, "Dashboard des IGs en cours");
        /**
         * **** FIN LOG APPLICATIF *****
         */
        
        $tri = $this->params('tri');
        
        $this->calculEvolutionIg->calculer_evolution_ticket();
        
        $idTicket = trim($this->params()->fromPost('id_ticket'));
        $idCategorie = trim($this->params()->fromPost('categorie'));
        $apli = trim($this->params()->fromPost('apli'));
        $idSi = trim($this->params()->fromPost('si'));
        $desc_incident = trim($this->params()->fromPost('desc_incident'));
        $debut_incident = trim($this->params()->fromPost('debut_incident'));
        $proc_evo = trim($this->params()->fromPost('proc_evo'));
        $ig_depart = trim($this->params()->fromPost('ig_depart'));
        $ig_encours = trim($this->params()->fromPost('ig_encours'));
        $ig_proc = trim($this->params()->fromPost('ig_proc'));
        $pilote = trim($this->params()->fromPost('pilote'));
        $igEnAttente = $this->ticketControl->IgEnAttente();
        
        $dashboard = $this->ticketControl->dashboard($idTicket, $idCategorie, $apli, $idSi, $desc_incident, $debut_incident, $proc_evo, $ig_depart, $ig_encours, $ig_proc, $pilote, $tri);
        
        $paginator = new Paginator(new Adapter\ArrayAdapter($dashboard->toArray()));
        $num_page = $this->params()->fromQuery('num_page', 0);
        $paginator->setCurrentPageNumber($num_page);
        
        
        return [
            'tri' => $tri,
            'idTicket' => $idTicket,
            'idCategorie' => $idCategorie,
            'apli' => strtolower($apli),
            'idSi' => $idSi,
            'desc_incident' => $desc_incident,
            'debut_incident' => $debut_incident,
            'proc_evo' => $proc_evo,
            'ig_depart' => $ig_depart,
            'ig_encours' => $ig_encours,
            'ig_proc' => $ig_proc,
            'pilote' => $pilote,
            'igDashboard' => $this->igDashboard,
            'calculDate' => $this->calculDate,
            'message' => $this->message,
            'igEnAttente' => $igEnAttente,
            'paginator' => $paginator,
            'actionName' => $this->actionName
            ];
    }

    public function igdumoisAction()
    {
        $this->layout()->setVariable('subTitle', 'Bilan des IG du mois');
        
        /**
         * ****** LOG APPLICATIF *******
         */
        $this->logApplication("affichage", 0, "Dashboard des IGs du mois");
        /**
         * **** FIN LOG APPLICATIF *****
         */
        
        $IGEnCours = $this->ticketControl->IGEnCours();
        $paginator = new Paginator(new Adapter\ArrayAdapter($IGEnCours->toArray()));
        $num_page = $this->params()->fromQuery('num_page', 0);
        $paginator->setCurrentPageNumber($num_page);
        
        $this->calculEvolutionIg->calculer_evolution_ticket();
        
        return [
            'paginator' => $paginator,
            'igDashboard' => $this->igDashboard,
            'calculDate' => $this->calculDate,
        ];
    }

    public function piloteAction()
    {
        $this->layout()->setVariable('subTitle', 'Pilote');
        
        $this->layout()->setTemplate('layout/layout-all');
        
        $pilote = $this->params()->fromQuery('pilote', '');
        $id_ticket = $this->params()->fromQuery('id_ticket', '');
        $this->pilote->setData([
            'ancienpilote' => $pilote,
            'id_ticket' => $id_ticket
        ]);
        
        /**
         * ****** LOG APPLICATIF *******
         */
        $this->logApplication("affichage", 0, "Affichage du pilote: " . $pilote);
        /**
         * **** FIN LOG APPLICATIF *****
         */
        
        return [
            'form' => $this->pilote
        ];
    }

    public function messageAction()
    {
        $this->layout()->setVariable('subTitle', 'Message');
        
        $this->layout()->setTemplate('layout/layout-all');
        
        $IG = $this->params()->fromQuery('IG', '');
        $categorie_service = $this->params()->fromQuery('categorie_service', '');
        $id_ticket = $this->params()->fromQuery('id_ticket', '');
        
        /**
         * ****** LOG APPLICATIF *******
         */
        $this->logApplication("affichage", 0, "Affichage du message du ticket: " . $id_ticket . " - IG: " . $IG);
        /**
         * **** FIN LOG APPLICATIF *****
         */
        
        $message = $this->messageDashboard->find($IG, $categorie_service);
        
        return new ViewModel([
            'message' => $message,
            'id_ticket' => $id_ticket,
            'IG' => $IG,
            'categorie_service' => $categorie_service
        ]);
    }

    public function insertpiloteAction()
    {
        $form = $this->pilote;
        $view = new ViewModel();
        $view->setTerminal(true); // desactive le layout
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);
            if ($form->isValid()) {
                
                $this->ticketControl->update([
                    'nom_admin' => stripslashes($formData['pilote'])
                ], [
                    'id_ticket' => $formData['id_ticket']
                ]);
                
                /**
                 * ****** LOG APPLICATIF *******
                 */
                $this->logApplication("modification", 1, "Modification du pilote: " . $formData['pilote']);
                /**
                 * **** FIN LOG APPLICATIF *****
                 */
                
                echo '<script>self.opener.location.reload();</script>';
                echo '<script>window.close()</script>';
            }
            
            $view->setVariables([
                'form' => $form
            ]);
            
            $view->setTemplate("admin/dashboard/pilote");
            return $view;
        }
    }

    public function updatemessageAction()
    {
        if ($this->getRequest()->isPost()) {
            /**
             * ****** LOG APPLICATIF *******
             */
            $this->logApplication("insertion", 1, "Insertion d'un message pour le ticket: " . $_POST['id_ticket'] . " - IG: " . $_POST['IG']);
            /**
             * **** FIN LOG APPLICATIF *****
             */
            
            $formData = $this->getRequest()->getPost();
            $tab = [
                'IG' => $formData['IG'],
                'categorie_service' => $formData['categorie_service'],
                'id_ticket' => $formData['id_ticket'],
                'commentaire' => $formData['Commentaire'],
                'enreg' => 1
            ];
            
            $this->message->insert($tab);
        }
        
        
        echo '<script>self.opener.location.reload();</script>';
        echo '<script>window.close()</script>';
    }

    public function dashboardsAction()
    {
        $this->layout()->setVariable('subTitle', 'Dashboards');
        
        /**
         * ****** LOG APPLICATIF *******
         */
        $this->logApplication("affichage", 0, "Dashboards de toutes les entités");
        /**
         * **** FIN LOG APPLICATIF *****
         */
        $igEnAttente = $this->ticketControl->IgEnAttente();
        return [
            'igEnAttente' => $igEnAttente,
            'si' => $this->ticketControl->dashboard(),
            'bureautique' => $this->othersIg->dashboardIgBureautique(),
            'igDashboard' => $this->igDashboard,
            'calculDate' => $this->calculDate,
        ];
    }

    public function murimageAction()
    {
        $this->layout()->setTemplate('layout/layout-all');
        
        /**
         * ****** LOG APPLICATIF *******
         */
        $this->logApplication("affichage", 0, "Dashboard du mur d'image");
        /**
         * **** FIN LOG APPLICATIF *****
         */
        
        $tri = $this->params('tri');
        
        $this->calculEvolutionIg->calculer_evolution_ticket();
        
        $idTicket = trim($this->params()->fromPost('id_ticket'));
        $idCategorie = trim($this->params()->fromPost('categorie'));
        $apli = trim($this->params()->fromPost('apli'));
        $idSi = trim($this->params()->fromPost('si'));
        $desc_incident = trim($this->params()->fromPost('desc_incident'));
        $debut_incident = trim($this->params()->fromPost('debut_incident'));
        $proc_evo = trim($this->params()->fromPost('proc_evo'));
        $ig_depart = trim($this->params()->fromPost('ig_depart'));
        $ig_encours = trim($this->params()->fromPost('ig_encours'));
        $ig_proc = trim($this->params()->fromPost('ig_proc'));
        $pilote = trim($this->params()->fromPost('pilote'));
        $igEnAttente = $this->ticketControl->IgEnAttente();
        
        $dashboard = $this->ticketControl->dashboard($idTicket, $idCategorie, $apli, $idSi, $desc_incident, $debut_incident, $proc_evo, $ig_depart, $ig_encours, $ig_proc, $pilote, $tri);
        
        $paginator = new Paginator(new Adapter\ArrayAdapter($dashboard->toArray()));
        $num_page = $this->params()->fromQuery('num_page', 0);
        $paginator->setCurrentPageNumber($num_page);
        
        return [
            'tri' => $tri,
            'idTicket' => $idTicket,
            'idCategorie' => $idCategorie,
            'apli' => strtolower($apli),
            'idSi' => $idSi,
            'desc_incident' => $desc_incident,
            'debut_incident' => $debut_incident,
            'proc_evo' => $proc_evo,
            'ig_depart' => $ig_depart,
            'ig_encours' => $ig_encours,
            'ig_proc' => $ig_proc,
            'pilote' => $pilote,
            'igDashboard' => $this->igDashboard,
            'calculDate' => $this->calculDate,
            'message' => $this->message,
            'igEnAttente' => $igEnAttente,
            'paginator' => $paginator,
            'actionName' => $this->actionName
            
        ];
    }
}
