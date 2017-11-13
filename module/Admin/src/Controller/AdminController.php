<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Admin\Controller;

use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Session\Container;
use Classes\MyLibrary\MyLogApplicatif;
use Admin\Model\DbTable;
use Admin\Form;
use Calcul\Form\Calcul;
use Application\Controller\BaseController;

class AdminController extends BaseController
{
    // Pour réutilisation de methodes logApplication et historisation de la librairie devoteamclasses dans le dossier vendor
    protected $db, $logControllerPage, $logActionPage, $logTypeAction, $logapplicatif, $msg, $categorie, $ponderation, $pond, $ticketControl, $evolutionPrevu, $evolutionTicket, $historiqueMail, $historiqueSms, $controlGel;

    protected $formAjouterCategorie, $calcul;


    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        $this->container = new Container('initialized');
        
        $this->layout()->setVariable('page', 'administration');
        
        $this->actionName = $this->params('action');
        $this->layout()->setVariable('nom_action', $this->actionName);
        $this->controllerName = $this->params('controller');
        $this->layout()->setVariable('nom_controller', $this->controllerName);
        parent::dispatch($request, $response);
    }

    public function __construct(AdapterInterface $db, MyLogApplicatif $log, DbTable\HistoriqueModifications $histo, \Application\Model\DbTable\LogControllerPage $logControllerPage, \Application\Model\DbTable\LogActionPage $logActionPage, \Application\Model\DbTable\LogTypeAction $logTypeAction, \Application\Model\DbTable\LogApplicatif $logapplicatif, \Admin\Model\DbTable\MessageDashboard $msg, DbTable\CategorieServices $categorie, DbTable\Ponderations $ponderation, DbTable\PonderationsDynamique $pond, DbTable\TicketControl $ticketControl, 

    DbTable\EvolutionPrevu $evolutionPrevu, DbTable\EvolutionTicket $evolutionTicket, DbTable\HistoriqueMail $historiqueMail, DbTable\HistoriqueSms $historiqueSms, DbTable\ControlGel $controlGel, 

    Form\AjouterCategorie $formAjouterCategorie, Calcul $calcul)
    {
        $this->db = $db;
        $this->log = $log;
        $this->histo = $histo;
        $this->msg = $msg;
        $this->logController = $logControllerPage;
        $this->logActionPage = $logActionPage;
        $this->logTypeAction = $logTypeAction;
        $this->logapplicatif = $logapplicatif;
        $this->categorie = $categorie;
        $this->ponderation = $ponderation;
        $this->pond = $pond;
        $this->ticketControl = $ticketControl;
        $this->evolutionPrevu = $evolutionPrevu;
        $this->evolutionTicket = $evolutionTicket;
        $this->historiqueMail = $historiqueMail;
        $this->historiqueSms = $historiqueSms;
        $this->controlGel = $controlGel;
        
        $this->formAjouterCategorie = $formAjouterCategorie;
        $this->calcul = $calcul;
    }

    public function indexAction()
    {
        $this->layout()->setVariable('subTitle', 'Matrice IG dynamique');
        
        /**
         * ****** LOG APPLICATIF *******
         */
        $this->logApplication("affichage", 0, "Affichage de la matrice dynamique");
        /**
         * **** FIN LOG APPLICATIF *****
         */
        
        return [
            'categorie' => $this->categorie->selectCS(),
            'ponderation' => $this->pond->selectDyn()
        ];
    }

    public function insertAction()
    {
        if ($this->getRequest()->isPost()) {
            /**
             * ****** LOG APPLICATIF *******
             */
            $this->logApplication("modification", 0, "Modification de la matrice dynamique");
            /**
             * **** FIN LOG APPLICATIF *****
             */
            
            $formData = $this->params()->fromPost();
            /* $ponderation->delete(''); */
            foreach ($formData as $k => $v) {
                if ($k != 'Enregistrer' && ! strstr($k, 'Pas')) {
                    $tmp = explode('@', $k);
                    $tab = array(
                        'id_typo' => $tmp[0],
                        'id_duree' => $tmp[1],
                        'id_etat' => $tmp[2],
                        'ponderation' => $v
                    );
                    $this->pond->update($tab, [
                        'id_typo' => $tmp[0],
                        'id_duree' => $tmp[1],
                        'id_etat' => $tmp[2]
                    ]);
                }
            }
            
            /**
             * ** Historique Modifications**
             */
            $this->historisation('Matrice IG', 'Modification de la matrice IG dynamique');
        /**
         * ** Fin Historique Modifications**
         */
        }
        
        $this->redirect()->toRoute('admin', [
            'action' => 'index'
        ]);
    }

    public function matriceAction()
    {
        $this->layout()->setVariable('subTitle', 'Matrice IG standard');
        
        /**
         * ****** LOG APPLICATIF *******
         */
        $this->logApplication("affichage", 0, "Affichage de la matrice standard");
        /**
         * **** FIN LOG APPLICATIF *****
         */
        
        return [
            'categorie' => $this->categorie->selectCS(),
            'ponderation' => $this->ponderation->selectVal()
        ];
    }

    public function insertmatricestdAction()
    {
        if ($this->getRequest()->isPost()) {
            /**
             * ****** LOG APPLICATIF *******
             */
            $this->logApplication("modification", 1, "Modification de la matrice standard");
            /**
             * **** FIN LOG APPLICATIF *****
             */
            
            $formData = $this->params()->fromPost();
            // $this->ponderation->delete('');
            
            foreach ($formData as $k => $v) {
                if ($k != 'Enregistrer' && ! strstr($k, 'Pas')) {
                    $tmp = explode('@', $k);
                    
                    $tab = [
                        'id_typo' => $tmp[0],
                        'id_duree' => $tmp[1],
                        'id_etat' => $tmp[2],
                        'ponderation' => $v
                    ];
                    $this->ponderation->update($tab, [
                        'id_typo' => $tmp[0],
                        'id_duree' => $tmp[1],
                        'id_etat' => $tmp[2]
                    ]);
                }
            }
            
            /**
             * ** Historique Modifications**
             */
            $this->historisation('Matrice IG', 'Modification de la matrice Matrice IG standard');
        /**
         * ** Fin Historique Modifications**
         */
        }
        
        $this->redirect()->toRoute('admin', [
            'action' => 'matrice'
        ]);
    }

    public function popupAction()
    {
        $view = new ViewModel([
            'categorie' => $this->categorie->selectCS(),
            'ponderation' => $this->pond->selectDyn()
        ]);
        
        $view->setTerminal(true);
        
        return $view;
    }

    public function ajoutercategorieAction()
    {
        return [
            'form' => $this->formAjouterCategorie
        ];
    }

    public function insertcategorieAction()
    {
        if ($this->getRequest()->isPost()) {
            $formData = $this->params()->fromPost();
            $this->formAjouterCategorie->setData($formData);
            
            if ($this->formAjouterCategorie->isValid()) {
                unset($formData['submit']);
                $result = $this->categorie->insert($formData);
                if ($result)
                    // Add a flash message.
                    $this->flashMessenger()->addSuccessMessage('Sauvegarde reussie');
                else
                    $this->flashMessenger()->addSuccessMessage('Echec de sauvegarde');
                
                $this->redirect()->toRoute('admin', [
                    'action' => 'ajoutercategorie'
                ]);
            }
        }
        
        $view = new ViewModel([
            'form' => $this->formAjouterCategorie
        ]);
        $view->setTemplate('admin/ajoutercategorie');
        return $view;
    }

    public function modifiercategorieAction()
    {
        return [
            'categories' => $this->categorie->selectCS()
        ];
    }

    public function updatecategorieAction()
    {
        if ($this->getRequest()->isPost()) {
            $formData = $this->params()->fromPost();
            /* $cat->delete(''); */
            for ($i = 0; $i < sizeof($_POST['id_typo']); $i ++) {
                if ($_POST['id_typo'][$i] != '' && $_POST['typo_categorie'][$i] != '' && $_POST['id'][$i] != '') {
                    $tab = [
                        'id_typo' => $_POST['id_typo'][$i],
                        'typo_categorie' => $_POST['typo_categorie'][$i],
                        'id' => $_POST['id'][$i]
                    ];
                    $result = $this->categorie->update($tab, [
                        'id_typo' => $_POST['id_typo'][$i]
                    ]);
                }
            }
            
            // Add a flash message.
            if ($result)
                // Add a flash message.
                $this->flashMessenger()->addSuccessMessage('Modification reussie');
            else
                $this->flashMessenger()->addSuccessMessage('Echec Modification');
        }
        
        $this->redirect()->toRoute('admin', [
            'action' => 'modifiercategorie'
        ]);
    }

    public function supprimerAction()
    {
        $this->calcul->setType('supprimer');
        $this->calcul->addElements();
        
        return [
            'form' => $this->calcul
        ];
    }

    public function supprimerticketAction()
    {
        $this->calcul->setType('supprimerTicket');
        $this->calcul->addElements();
        if (isset($_POST['id_ticket']) && $_POST['id_ticket'] != '')
            $data = $this->ticketControl->findByTicket($_POST['id_ticket']);
            // On regarde si le n° de ticket est dans la base
        if (isset($_POST['id_ticket']) && sizeof($data) <= 0) {
            $this->flashMessenger()->addErrorMessage("Le ticket n'existe pas, veuillez en saisir un nouveau !");
            return $this->redirect()->toRoute('admin', [
                'action' => 'supprimer'
            ]);
        } elseif (isset($data)) {
            foreach ($data as $v) {
                $tab['id_ticket'] = $v->id_ticket;
                $tab['typo_service'] = $v->categorie_service . '_@' . $v->typo_service;
                $tab['debut_incident'] = $v->debut_incident;
                $tab['categorie_service'] = $v->categorie_service;
                $tab['nb_service'] = $v->nb_service;
                $tab['etat_service'] = $v->etat_service;
                $tab['visibilite'] = $v->visibilite;
                $tab['duree_incident'] = $v->duree_incident;
                $tab['busy_hour'] = $v->busy_hour;
                $tab['IG'] = $v->IG;
                $tab['cloture'] = $v->cloture;
                $tab['nom_admin'] = $v->nom_admin;
                $tab['sous_systeme'] = $v->sous_systeme;
                $tab['Commentaire'] = $v->Commentaire;
            }
            
            /**
             * ****** LOG APPLICATIF *******
             */
            $this->logApplication("affichage", 0, "Affichage pour supprimer le ticket: " . $tab['id_ticket']);
            /**
             * **** FIN LOG APPLICATIF *****
             */
            
            if (isset($tab)) {
                $this->calcul->setData($tab);
            }
        }
        
        $view = new ViewModel([
            'form' => $this->calcul
        ]);
        $view->setTemplate('admin/supprimer');
        return $view;
    }

    public function deleteAction()
    {
        $result = $this->ticketControl->delete([
            'id_ticket' => $_POST['id_ticket']
        ]);
        $this->evolutionPrevu->delete([
            'id_ticket' => $_POST['id_ticket']
        ]);
        $this->evolutionTicket->delete([
            'id_ticket' => $_POST['id_ticket']
        ]);
        $this->historiqueMail->delete([
            'id_ticket' => $_POST['id_ticket']
        ]);
        $this->historiqueSms->delete([
            'id_ticket' => $_POST['id_ticket']
        ]);
        // modification historique
        $this->histo->delete([
            'id_ticket' => $_POST['id_ticket']
        ]);
        $this->controlGel->delete([
            'id_ticket' => $_POST['id_ticket']
        ]);
        
        if ($result) {
            /**
             * ****** LOG APPLICATIF *******
             */
            $this->logApplication("suppression", 1, "Suppression du ticket: " . $_POST['id_ticket']);
            /**
             * **** FIN LOG APPLICATIF *****
             */
            $this->flashMessenger()->addSuccessMessage("Suppression reussie!");
        } else
            $this->flashMessenger()->addSuccessMessage("Echec de la Suppression!");
        
        $this->redirect()->toRoute('admin', [
            'action' => 'supprimer'
        ]);
    }

    public function descoperAction()
    {
        $this->calcul->setType('descoper');
        $this->calcul->addElements();
        
        return [
            'form' => $this->calcul
        ];
    }

    public function descoperticketAction()
    {
        $view = new ViewModel();
        $this->calcul->setType('descoper');
        $this->calcul->addElements();
        
        if (isset($_POST['id_ticket']) && $_POST['id_ticket'] != '')
            $data = $this->ticketControl->findByTicket($_POST['id_ticket']);
            
            // On regarde si le n° de ticket est dans la base
        if (isset($_POST['id_ticket']) && sizeof($data) <= 0) {
            $this->flashMessenger()->addErrorMessage("Le ticket n'existe pas, veuillez en saisir un nouveau !");
            return $this->redirect()->toRoute('admin', [
                'action' => 'descoper'
            ]);
        } elseif (isset($data)) {
            foreach ($data as $v) {
                $tab['id_ticket'] = $v->id_ticket;
                $tab['typo_service'] = $v->categorie_service . '_@' . $v->typo_service;
                $tab['debut_incident'] = $v->debut_incident;
                $tab['categorie_service'] = $v->categorie_service;
                $tab['nb_service'] = $v->nb_service;
                $tab['etat_service'] = $v->etat_service;
                $tab['visibilite'] = $v->visibilite;
                $tab['duree_incident'] = $v->duree_incident;
                $tab['busy_hour'] = $v->busy_hour;
                $tab['IG'] = $v->IG;
                $tab['cloture'] = $v->cloture;
                $tab['nom_admin'] = $v->nom_admin;
                $tab['sous_systeme'] = $v->sous_systeme;
                $tab['Commentaire'] = $v->Commentaire;
            }
            
            /**
             * ****** LOG APPLICATIF *******
             */
            $this->logApplication("affichage", 0, "Affichage pour descoper le ticket : " . $tab['id_ticket']);
            /**
             * **** FIN LOG APPLICATIF *****
             */
            
            $this->calcul->setData($tab);
            
            $view->setVariables([
                'cloture' => $tab['cloture']
            ]);
        }
        
        $view->setVariables([
            'form' => $this->calcul
        ]);
        $view->setTemplate('admin/descoper');
        return $view;
    }

    public function enregdescoperAction()
    {
        $this->calcul->setType('descoper');
        $this->calcul->addElements();
        if ($this->getRequest()->isPost()) {
            $formData = $this->params()->fromPost();
            $this->calcul->setData($formData);
            $this->calcul->addInputFilter();
            $this->calcul->setValidationGroup('dsko_commentaire');
            if ($this->calcul->isValid()) {
                $result = $this->ticketControl->update([
                    'dsko_ig' => 1,
                    'dsko_commentaire' => $_POST['dsko_commentaire']
                ], [
                    'id_ticket' => $_POST['id_ticket']
                ]);
                
                if ($result) {
                    /**
                     * ****** LOG APPLICATIF *******
                     */
                    $this->logApplication("descoper", 1, "Descopage du ticket : " . $_POST['id_ticket']);
                    /**
                     * **** FIN LOG APPLICATIF *****
                     */
                    
                    $this->flashMessenger()->addSuccessMessage("Sauvegarde reussie!");
                } else
                    $this->flashMessenger()->addSuccessMessage("Echec de sauvegarde!");
                
                return $this->redirect()->toRoute('admin', [
                    'action' => 'descoper'
                ]);
            }
        }
        
        $view = new ViewModel([
            'form' => $this->calcul
        ]);
        $view->setTemplate('admin/descoper');
        return $view;
    }

    public function messageAction()
    {
        return new ViewModel([
            'categorie' => $this->categorie->selectCS()->toArray(),
            'message' => $this->msg->findAll()
        ]);
    }

    public function updatemessageAction()
    {
        if ($this->getRequest()->isPost()) {
            $formData = $this->params()->fromPost();
            foreach ($formData['IG'] as $key => $IG) {
                if (! empty($formData['message'][$key])) {
                    $IG = floatval($formData['IG'][$key]);
                    $categorie_service = $formData['categorie_service'][$key];
                    $message = utf8_encode($formData['message'][$key]);
                    $this->msg->updateMessage($IG, $categorie_service, $message);
                }
            }
        }
        $view = $this->messageAction();
        $view->setTemplate('admin/message');
        return $view;
    }

    public function logdashboardAction()
    {
        $view = new ViewModel();
        $path = $this->getRequest()->getBaseUrl();
        $tri = $this->params('tri');
        
        if (! isset($tri))
            $tri = "date_log_desc";
        
        $limit = 50;
        $insertion_bdd = 0;
        
        $list_controller = $this->logController->select();
        $list_action = $this->logActionPage->select();
        $list_type_action = $this->logTypeAction->select();
        
        $view->setVariable('list_controller', $list_controller);
        $view->setVariable('list_action', $list_action);
        $view->setVariable('list_type_action', $list_type_action);
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->params()->fromPost();
            
            $limit = $formData['limit'];
            if (isset($formData['insertion_bdd']))
                $insertion_bdd = 1;
            else
                $insertion_bdd = 0;
            
            if (isset($formData['controller_page']))
                $controller_page = $formData['controller_page'];
            else
                $controller_page = array();
            
            if (isset($formData['action_page']))
                $action_page = $formData['action_page'];
            else
                $action_page = array();
            
            if (isset($formData['type_action']))
                $type_action = $formData['type_action'];
            else
                $type_action = array();
            
            $id_log = $formData['id_log'];
            $ip_user = $formData['ip_user'];
            $date_log_sup = $formData['date_log_sup'];
            $date_log_inf = $formData['date_log_inf'];
            $login = $formData['login'];
            
            $dashboard = $this->logapplicatif->afficheAllFiltre($limit, $insertion_bdd, $id_log, $ip_user, $date_log_sup, $date_log_inf, $controller_page, $action_page, $type_action, $login, $tri);
            
            $view->setVariable('dashboard', $dashboard);
            $view->setVariable('path', $path);
            $view->setVariable('tri', $tri);
            $view->setVariable('limit', $limit);
            $view->setVariable('insertion_bdd', $insertion_bdd);
            $view->setVariable('id_log', $id_log);
            $view->setVariable('date_log_sup', $date_log_sup);
            $view->setVariable('date_log_inf', $date_log_inf);
            $view->setVariable('controller_page', $controller_page);
            $view->setVariable('login', $login);
        }
        
        $this->layout()->setVariable('subTitle', "Dashboard des $limit derniers logs applicatifs");
        
        return $view;
    }
}
