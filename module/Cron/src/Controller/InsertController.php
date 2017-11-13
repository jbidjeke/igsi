<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Cron\Controller;

use RuntimeException;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\Db\Adapter\AdapterInterface;
use Cron\Model\DbTable;
use Application\Controller\BaseController;
use Zend\Console\Request as ConsoleRequest;
// use Zend\Db\Adapter;
class InsertController extends BaseController
{

    protected $db, $esmsApp, $igApp, $config;

    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        parent::dispatch($request, $response);
    }

    public function __construct($config, AdapterInterface $db, DbTable\EsmsApp $esmsApp, DbTable\IgApp $igApp)
    {
        $this->config = $config;
        $this->db = $db;
        $this->esmsApp = $esmsApp;
        $this->igApp = $igApp;
    }

    /* Inserer des données dans la table db_igsi.esms_app */
    public function fichigsiesmsappAction()
    {
        $request = $this->getRequest();
        
        // Make sure that we are running in a console, and the user has not
        // tricked our application into running this action from a public web
        // server:
        if (! $request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }
        
        // Get EsmsApp directory from the console
        $dirEsmsApp = $this->config["csv"]['datain'];
        $file = 'esms_app.txt';
        
        /**
         * * Lecture du fichier .
         *
         * ./igsi/datain/esms_app.txt**
         */
        try {
            /**
             * ****************************
             */
            $logImport = "LOG\n";
            if (file_exists($dirEsmsApp . $file)) // e.g: ../igsi/datain/esms_app.txt
{
                $this->igApp->insertEsmsApp($dirEsmsApp . $file);
                $this->igApp->insertEsmsAppServices($dirEsmsApp . $file);
                $this->igApp->updateIgsiEsmsAppWeb();
                $logImport .= "Insert esms_app.txt\n";
            } else {
                $this->igApp->insertEsmsApp($dirEsmsApp . 'esms_app_bckup.txt');
                $this->igApp->insertEsmsAppServices($dirEsmsApp . 'esms_app_bckup.txt');
                $this->igApp->updateIgsiEsmsAppWeb();
                $logImport .= "Insert esms_app_bckup.txt\n";
            }
        /**
         * ****************************
         */
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        
        echo "Done!, fichigsiesmsapp";
    }

    public function fichesmsAction()
    {
        $request = $this->getRequest();
        
        // Make sure that we are running in a console, and the user has not
        // tricked our application into running this action from a public web
        // server:
        if (! $request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }
        
        // Get EsmsApp directory from the console
        $dirEsmsApp = $this->config["csv"]['datain'];
        $file = 'esms_app.txt';
        
        /**
         * * Lecture du fichier /igsi/datain/esms_app.txt**
         */
        try {
            /**
             * ****************************
             */
            $logImport = "LOG\n";
            if (file_exists($dirEsmsApp . $file)) // e.g: /varsoft/igsi/datain/esms_app.txt
{
                $this->esmsApp->insertEsmsApp($dirEsmsApp . $file);
                $this->esmsApp->updateEsmsAppWeb();
                $this->esmsApp->updateEsmsAppSpp();
                $logImport .= "Insert esms_app.txt\n";
            } else {
                $this->esmsApp->insertEsmsApp($dirEsmsApp . 'esms_app_bckup.txt');
                $this->esmsApp->updateEsmsAppWeb();
                $this->esmsApp->updateEsmsAppSpp();
                $logImport .= "Insert esms_app_bckup.txt\n";
            }
            /**
             * ****************************
             */
            if (file_exists($dirEsmsApp . 'esms_abo.txt')) {
                $this->esmsApp->insertEsmsAbo($dirEsmsApp . 'esms_abo.txt');
                $logImport .= "Insert esms_abo.txt\n";
            } else {
                $this->esmsApp->insertEsmsAbo($dirEsmsApp . 'esms_abo.txt');
                $logImport .= "Insert esms_abo_bckup.txt\n";
            }
            /**
             * ****************************
             */
            if (file_exists($dirEsmsApp . 'esms_abo_list.txt')) {
                $this->esmsApp->insertEsmsAboList($dirEsmsApp . 'esms_abo_list.txt');
                $logImport .= "Insert esms_abo_list.txt\n";
            } else {
                $this->esmsApp->insertEsmsAboList($dirEsmsApp . 'esms_abo_list_bckup.txt');
                $logImport .= "Insert esms_abo_list_bckup.txt\n";
            }
            /**
             * ****************************
             */
            if (file_exists($dirEsmsApp . 'esms_user.txt')) {
                $this->esmsApp->insertEsmsUser($dirEsmsApp . 'esms_user.txt');
                $logImport .= "Insert esms_user.txt\n";
            } else {
                $this->esmsApp->insertEsmsUser('esms_user_bckup.txt');
                $logImport .= "Insert esms_user_bckup.txt\n";
            }
            /**
             * ****************************
             */
            if (file_exists($dirEsmsApp . 'esms_user_abo.txt')) {
                $this->esmsApp->insertEsmsUserAbo($dirEsmsApp . 'esms_user_abo.txt');
                $logImport .= "Insert esms_user_abo.txt\n";
            } else {
                $this->esmsApp->insertEsmsUserAbo($dirEsmsApp . 'esms_user_abo_bckup.txt');
                $logImport .= "Insert esms_user_abo_bckup.txt\n";
            }
            /**
             * ****************************
             */
            if (file_exists($dirEsmsApp . 'esms_user_abo_list.txt')) {
                $this->esmsApp->insertEsmsUserAboList($dirEsmsApp . 'esms_user_abo_list.txt');
                $logImport .= "Insert esms_user_abo_list.txt\n";
            } else {
                $this->esmsApp->insertEsmsUserAboList($dirEsmsApp . 'esms_user_abo_list_bckup.txt');
                $logImport .= "Insert esms_user_abo_list_bckup.txt\n";
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        
        echo "Done!, fichesms";
    }

   

    /* Split ig dans la table esms_abo */
    public function splitigesmsAction()
    {
        $request = $this->getRequest();
        
        // Make sure that we are running in a console, and the user has not
        // tricked our application into running this action from a public web
        // server:
        if (! $request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }
        
        // Get EsmsApp directory from the console
        // $dirEsmsApp = $this->config["csv"]['datain'];
        // $file = 'esms_app.txt';
        
        try {
            /**
             * ****************************
             */
            $logSplitIG = "Split champ ig\n";
            if ($this->esmsApp->checkBd('esms_abo')) {
                $logSplitIG .= "Table esms_abo exist\n";
                $this->esmsApp->splitIG();
                $logSplitIG .= "Split du champ IG realise\n";
            } else {
                $logSplitIG .= "Split NON realisee\n";
            }
        /**
         * ****************************
         */
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        
        echo "Done!, splitigesms";
    }


}
