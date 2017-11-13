<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;


use Application\Controller\BaseController;


class IgbureautiqueController extends BaseController
{    
    
    private $sessionManager;
    
    public function __construct($sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }
    
    public function indexAction()
    {  
        $this->redirect()->toUrl("http://10.153.152.172/toolbox/igBureautique/public/auth/session/id/".$this->sessionManager->getId()); 

    }
}
