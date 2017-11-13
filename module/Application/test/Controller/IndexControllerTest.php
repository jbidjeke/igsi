<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ApplicationTest\Controller;

use Application\Controller\IndexController;
use Admin\Controller\AdminController;
use Admin\Controller\DashboardController;
use Calcul\Controller\CalculController;
use Esms\Controller\SiController;
use Documentation\Controller\DocumentationController;
use Requeter\Controller\RequeterController;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
        defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'local');
        
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();
    }

    
    /**** 
     * TEST ACCESS
     *      
     */
    
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/', 'GET');
        $this->assertResponseStatusCode(500);
        $this->assertModuleName('application');
        $this->assertControllerName(IndexController::class); // as specified in router's controller name alias
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('home');
    }
    

    public function testInvalidRouteDoesNotCrash()
    {
        $this->dispatch('/invalid/route', 'GET');
        $this->assertResponseStatusCode(404);
    }
    
    /*Admin test*/
    public function testAdminActionCanBeAccessed()
    {
        $this->dispatch('/admin', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('admin');
        $this->assertControllerName(AdminController::class); // as specified in router's controller name alias
        $this->assertControllerClass('AdminController');
        $this->assertMatchedRouteName('admin');
    }
    
    /*Dashboard test*/
    public function testDashboardActionCanBeAccessed()
    {
        $this->dispatch('/dashboard', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('admin');
        $this->assertControllerName(DashboardController::class); // as specified in router's controller name alias
        $this->assertControllerClass('DashboardController');
        $this->assertMatchedRouteName('dashboard');
    }
    
    /*Calcul test*/
    public function testCalculActionCanBeAccessed()
    {
        $this->dispatch('/calcul', 'GET');
        $this->assertResponseStatusCode(302);
        $this->assertModuleName('calcul');
        $this->assertControllerName(CalculController::class); // as specified in router's controller name alias
        $this->assertControllerClass('CalculController');
        $this->assertMatchedRouteName('calcul');
    }
    
    /*Esms test*/
    public function testEsmsActionCanBeAccessed()
    {
        $this->dispatch('/si', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('esms');
        $this->assertControllerName(SiController::class); // as specified in router's controller name alias
        $this->assertControllerClass('SiController');
        $this->assertMatchedRouteName('si');
    }
    
    /*Requeter test*/
    public function testRequeterActionCanBeAccessed()
    {
        $this->dispatch('/requeter', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('requeter');
        $this->assertControllerName(RequeterController::class); // as specified in router's controller name alias
        $this->assertControllerClass('RequeterController');
        $this->assertMatchedRouteName('requete');
    }
    
    /*Documentation test*/
    public function testDocumentationActionCanBeAccessed()
    {
        $this->dispatch('/documentation', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('documentation');
        $this->assertControllerName(DocumentationController::class); // as specified in router's controller name alias
        $this->assertControllerClass('DocumentationController');
        $this->assertMatchedRouteName('documentation');
    }
    
    /**** 
     * TEST AFFICHAGE 
     *      
     */
    /* Test layout**/
    public function testIndexActionViewModelTemplateRenderedWithinLayout()
    {
        $this->dispatch('/', 'GET');
        $this->assertQuery('#wrap');
    }
    
    /* Test layout**/
    public function testDashboardIndexActionViewModelTemplateRenderedWithinLayout()
    {
        $this->dispatch('/dashboard', 'GET');
        $this->assertQuery('.ig_raw .action_a_realiser');
    }
}
