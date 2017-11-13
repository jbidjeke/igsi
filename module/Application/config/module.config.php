<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;


return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'igbureautique' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/igbureautique[/:action]',
                    'defaults' => [
                        'controller' => Controller\IgbureautiqueController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
 
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        //'base_path' => 'zf3-igSI/public/',
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    
    'controller_plugins' => [
        'factories' => [
            \Classes\Plugin\TranslatorPlugin::class => \Classes\Plugin\Factory\TranslatorPluginFactory::class,
            \Classes\Plugin\BaseUrlPlugin::class => \Classes\Plugin\Factory\BaseUrlPluginFactory::class,
        ],
        'aliases' => [
            'translator' => \Classes\Plugin\TranslatorPlugin::class,
            'baseUrl' => \Classes\Plugin\BaseUrlPlugin::class,
        ],
    ],
    // We register module-provided view helpers under this key.
    'view_helpers' => [
        'factories' => [
            \Classes\Form\Helper\FormDecorator::class => \Classes\Form\Helper\Factory\FormDecoratorFactory::class,
            \Classes\Helper\BaseUrl::class => \Classes\Helper\Factory\BaseUrlFactory::class,
            \Classes\Helper\Notice::class => \Classes\Helper\Factory\NoticeFactory::class,
            \Classes\Menu\Helper\MenuGauche::class => \Classes\Menu\Helper\Factory\MenuFactory::class,
            \Classes\Menu\Helper\MenuBas::class => \Classes\Menu\Helper\Factory\MenuFactory::class,
            \Classes\Menu\Helper\MenuHaut::class => \Classes\Menu\Helper\Factory\MenuFactory::class,
        ],
        'aliases' => [
            'formDecorator' => \Classes\Form\Helper\FormDecorator::class,
            'baseUrl' => \Classes\Helper\BaseUrl::class,
            'notice' => \Classes\Helper\Notice::class,
            'menuGauche' => \Classes\Menu\Helper\MenuGauche::class,
            'menuBas' => \Classes\Menu\Helper\MenuBas::class,
            'menuHaut' => \Classes\Menu\Helper\MenuHaut::class,
        ],
    ],
];
