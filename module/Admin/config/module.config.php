<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin;

//use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'admin' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/admin[/:action]',
                    'defaults' => [
                        'controller' => \Admin\Controller\AdminController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'dashboard' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/dashboard[/:action[/:tri]]',
                    'defaults' => [
                        'controller' => \Admin\Controller\DashboardController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
			'dashboardbur' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/dashboardbur[/:action[/:tri]]',
                    'defaults' => [
                        'controller' => \Admin\Controller\DashboardburController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    /*'controllers' => [
        'factories' => [
            
        ],
    ],*/
    'view_manager' => [
        'template_path_stack' => [
            'admin' => __DIR__ . '/../view',
        ],
    ],
];
