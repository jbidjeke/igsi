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
class DeleteController extends BaseController
{

    protected $db, $esmsApp, $igApp, $config;

    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        parent::dispatch($request, $response);
    }

    public function __construct($config, AdapterInterface $db, DbTable\EsmsApp $esmsApp)
    {
        $this->config = $config;
        $this->db = $db;
        $this->esmsApp = $esmsApp;
    }

    public function suppressionhistoAction()
    {
        $request = $this->getRequest();
        
        // Make sure that we are running in a console, and the user has not
        // tricked our application into running this action from a public web
        // server:
        if (! $request instanceof ConsoleRequest) {
            throw new RuntimeException('You can only use this action from a console!');
        }
        
        $liste_service = array(
            'adg',
            'bur',
            'esp',
            'nms',
            'si',
            'noc'
        );
        
        try {
            /**
             * ****************************
             */
            foreach ($liste_service as $service) {
                $this->esmsApp->suppressionHisto($service);
            }
        /**
         * ****************************
         */
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        
        echo "Done!";
    }
}