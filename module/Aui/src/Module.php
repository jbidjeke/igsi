<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Aui;

class Module 
{
    const VERSION = '3.0.3-dev';
    
    public function getServiceConfig()
    {
        return [
            'factories' => [ // registering a factories instance  
             
                Service\LdapLogin::class => Service\Factory\LdapLoginFactory::class
                
            ],
        ];
    }
}
