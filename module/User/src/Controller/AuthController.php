<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Result;
use Zend\Uri\Uri;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use User\Form\LoginForm;
use User\Entity\User;
use User\Service\UserManager;
use User\Service\AuthManager;
use Doctrine\ORM\EntityManager;
use Classes\Utile;

/**
 * This controller is responsible for letting the user to log in and log out.
 */
class AuthController extends AbstractActionController {

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager 
     */
    private $entityManager;

    /**
     * Auth manager.
     * @var User\Service\AuthManager 
     */
    private $authManager;

    /**
     * Auth service.
     * @var \Zend\Authentication\AuthenticationService
     */
    private $authService;

    /**
     * User manager.
     * @var User\Service\UserManager
     */
    private $userManager;

    /**
     * Session container.
     * @var Zend\Session\Container
     */
    private $container;

    /**
     * Login form.
     * @var User\Form\LoginForm
     */
    private $loginForm;
    private $mailer;

    /**
     * Constructor.
     */
    public function __construct(EntityManager $entityManager, AuthManager $authManager, AuthenticationService $authService, UserManager $userManager, LoginForm $loginForm, Utile\Mailer $mailer) {
        $this->entityManager = $entityManager;
        $this->authManager = $authManager;
        $this->authService = $authService;
        $this->userManager = $userManager;
        $this->loginForm = $loginForm;
        $this->mailer = $mailer;
    }

    public function dispatch(
    RequestInterface $request, ResponseInterface $response = null
    ) {

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

    /**
     * Authenticates user given email address and password credentials.     
     */
    public function loginAction() {
        //$this->layout()->setVariable('subTitle', 'Authentification');
        // Retrieve the redirect URL (if passed). We will redirect the user to this
        // URL after successfull login.
        $redirectUrl = (string) $this->params()->fromQuery('redirectUrl', '');
        if (strlen($redirectUrl) > 2048) {
            $this->flashMessenger()->addErrorMessage("L'argument de redirection est trop long.");
            return $this->redirect()->toRoute('home');
        }

        // Check if we do not have users in database at all. If so, create 
        // the 'Admin' user.
        $this->userManager->createAdminUserIfNotExists();

        // Create login form
        $form = $this->loginForm;

        // Store login status.
        $isLoginError = false;

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            // Fill in the form with POST data
            $data = $this->params()->fromPost();
            $step = $this->params()->fromPost('step');
            $form->setData($data);
            $form->get('step')->setValue($step);
            $form->setValidationGroup('full_name');

            //$isLoginError = true;
            if ($step == 1) {
                if ($data['role'] != "") { // Sauvegarde de demande de compte avec un status
                    $this->userManager->createOtherUserIfNotExists($this->container->username, $this->container->email, null, $data['role']);
                    // Add a flash message.
                    $this->flashMessenger()->addSuccessMessage('Sauvegarde de votre compte en attente de validation!');

                    $host = "mail-0092.sfr.com";
                    $sujet = "IG SI - Demande de profil « " . $data['role'] . " » en ATTENTE DE VALIDATION";
                    $body = "Bonjour,<br/><br/>

                    Une demande de profil « " . $data['role'] . " »  vient d'être formulée pour l'application IG SI.<br/>

                    Il vous revient de valider ou pas l'activation de ce droit.<br/>

                    Accédez à l'application et à l'IHM de gestion des utilisateurs.<br/>

                    Cordialement. 
                    ";
                    $from = 'no-replay-igsi@sfr.com';
                    try{
                        switch ($data['role']) {
                            case 'Administrateur':
                                $this->mailer->sendPHPMailer($body, $sujet, $host, null, null, 25, $from, null, null, "Admin-QF@encara.local.ads", null, null);
                                $this->mailer->sendPHPMailer($body, $sujet, $host, null, null, 25, $from, null, null, "olivier.badet@sfr.com", null, null);
                                $this->mailer->sendPHPMailer($body, $sujet, $host, null, null, 25, $from, null, null, "rene.laville@sfr.com", null, null);
                                break;
                            
                            /*case 'Pilote':
                                $this->mailer->sendPHPMailer($body, $sujet, $host, null, null, 25, $from, null, null, "Admin-IGSI@encara.local.ads", null, null);
                                break;
                            case 'Ls':
                                $this->mailer->sendPHPMailer($body, $sujet, $host, null, null, 25, $from, null, null, "Admin-IGSI@encara.local.ads", null, null);
                                break;*/
                            
                            default:
                                $this->mailer->sendPHPMailer($body, $sujet, $host, null, null, 25, $from, null, null, "Admin-IGSI@encara.local.ads", null, null);
                                $users = $this->entityManager->getRepository(User::class)->findBy([], ['id'=>'DESC']);
                                foreach($users as $user){
                                    if ($user->getRolesAsString() == "Administrateur")
                                        $this->mailer->sendPHPMailer($body, $sujet, $host, null, null, 25, $from, null, null, $user->getEmail(), null, null);   
                                    
                                }
                                
                        }
                    } catch (\Exception $e){

                    }
                    
                }
                           
                return $this->redirect()->toRoute('home');
            }

            // Validate form
            if ($form->isValid()) {
                // Get filtered and validated data
                if ($step == 0) {
                    // Perform login attempt.
                    $result = $this->authManager->login($data['full_name'], $data['password'], $data['remember_me']);
                    $this->container->username = $data['full_name'];
                }

                // Check result.
                if ($result->getCode() == Result::SUCCESS) {

                    if (strpos($result->getIdentity(), "@")) {
                        // Fetch User entity from database.
                        $user = $this->entityManager->getRepository(User::class)
                                ->findOneByEmail($result->getIdentity());
                    } else {
                        // Fetch User entity from database.
                        $user = $this->entityManager->getRepository(User::class)
                                ->findOneByFull_name($result->getIdentity());
                    }

                    // Get id and store it in session
                    $this->container->id_user = (int) $user->getId();

                    // Get redirect URL.
                    $redirectUrl = $this->params()->fromPost('redirect_url', '');

                    if (!empty($redirectUrl)) {
                        // The below check is to prevent possible redirect attack 
                        // (if someone tries to redirect user to another domain).
                        $uri = new Uri($redirectUrl);
                        if (!$uri->isValid() || $uri->getHost() != null) {
                            //throw new \Exception('Incorrect redirect URL: ' . $redirectUrl);
                            $this->flashMessenger()->addErrorMessage('Incorrect redirect URL: ' . $redirectUrl);
                            return $this->redirect()->toRoute('home');
                        }
                    }

                    // If redirect URL is provided, redirect the user to that URL;
                    // otherwise redirect to Home page.
                    if (empty($redirectUrl)) {
                        return $this->redirect()->toRoute('home');
                    } else {
                        $this->redirect()->toUrl($redirectUrl);
                    }
                } elseif ($result->getCode() == Result::FAILURE_UNCATEGORIZED) { // Redirection des comptes AUI dont la validation en attente 
                    //if (/*&& $data['check_me']*/){
                    // Add a flash message.
                    $this->flashMessenger()->addSuccessMessage('Votre compte en attente de validation!');
                    return $this->redirect()->toRoute('home');
                    //}
                } elseif ($result->getCode() == Result::FAILURE_IDENTITY_NOT_FOUND) { // Redirection des comptes AUI visiteur
                    $step = 1;
                    $form->get('step')->setValue($step);
                    //$isLoginError = 2;
                    $this->flashMessenger()->addInfoMessage(
                            "Merci de choisir un profil pour soumettre une demande d'activation de votre compte");

                    $email = $result->getLdap_entries()[0]['mail'][0];
                    $this->container->email = $email;
                } else
                    $isLoginError = true;
            } else {
                $isLoginError = true;
            }
        } else {
            $step = $this->params()->fromPost('step', 0);
            $form->get('step')->setValue($step);
        }


        $form->get('redirect_url')->setValue($redirectUrl);

        return new ViewModel([
            'form' => $form,
            'isLoginError' => $isLoginError,
            'redirectUrl' => $redirectUrl,
            'step' => $step
        ]);
    }

    /**
     * The "logout" action performs logout operation.
     */
    public function logoutAction() {
        if ($this->authManager->logout()) {
            return $this->redirect()->toRoute('login');
        } else
            return $this->redirect()->toRoute('home');
    }

    /**
     * Displays the "Not Authorized" page.
     */
    public function notAuthorizedAction() {
        $this->getResponse()->setStatusCode(403);

        return new ViewModel();
    }

}
