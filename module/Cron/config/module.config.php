<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Cron;

//use Zend\Router\Http\Literal;
//use Zend\Router\Http\Segment;

return [
    'console' =>[
        'router' => [
            'routes' => [
                /* delete */
               'esms-purge-histo-tables' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'esms_purge_histo_tables',
                        'defaults' => [
                            'controller' => Controller\DeleteController::class,
                            'action'     => 'suppressionhisto',
                        ],
                    ],
                ],
                
                /* Insert */
                'fich-igsi-esms-app' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'prod_insert_fich_igsi_esms_app',
                        'defaults' => [
                            'controller' => Controller\InsertController::class,
                            'action'     => 'fichigsiesmsapp',
                        ],
                    ],
                ],
                'fich-esms' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'prod_insert_fich_esms',
                        'defaults' => [
                            'controller' => Controller\InsertController::class,
                            'action'     => 'fichesms',
                        ],
                    ],
                ],
                'insert-fich-esms' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'prod_insert_fich_ig',
                        'defaults' => [
                            'controller' => Controller\InsertController::class,
                            'action'     => 'fichig',
                        ],
                    ],
                ],

                'prod-split-ig-esms' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'prod_split_IG_esms',
                        'defaults' => [
                            'controller' => Controller\InsertController::class,
                            'action'     => 'splitigesms',
                        ],
                    ],
                ],
                
                'prod-split-ig-ig' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'prod_split_IG_ig',
                        'defaults' => [
                            'controller' => Controller\InsertController::class,
                            'action'     => 'splitigig',
                        ],
                    ],
                ],
                
                
                'igbur-esms-app' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'prod_insert_fich_igbur_esms_app',
                        'defaults' => [
                            'controller' => Controller\InsertController::class,
                            'action'     => 'igburesmsapp',
                        ],
                    ],
                ],
                
                /* Generate csv */
                'extract-hebdo-esms-ec' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'extract hebdo_esms_ec',
                        'defaults' => [
                            'controller' => Controller\ExtractController::class,
                            'action'     => 'esmsechebdo',
                        ],
                    ],
                ],
                
                'extract-hebdo-esms' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'extract hebdo_esms',
                        'defaults' => [
                            'controller' => Controller\ExtractController::class,
                            'action'     => 'esmshebdo',
                        ],
                    ],
                ],
                
                'extract-hebdo-ig-ec' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'extract hebdo_ig_ec',
                        'defaults' => [
                            'controller' => Controller\ExtractController::class,
                            'action'     => 'igechebdo',
                        ],
                    ],
                ],

                'extract-hebdo-ig' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'extract hebdo_ig',
                        'defaults' => [
                            'controller' => Controller\ExtractController::class,
                            'action'     => 'ighebdo',
                        ],
                    ],
                ],
                
                'extract-six-months' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'extract sixmonths',
                        'defaults' => [
                            'controller' => Controller\ExtractController::class,
                            'action'     => 'sixmonths',
                        ],
                    ],
                ],
                
                'ig-to-promes' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'extract ig_to_promes',
                        'defaults' => [
                            'controller' => Controller\ExtractController::class,
                            'action'     => 'igtopromes',
                        ],
                    ],
                ],
                
                'generation-esms-en-cours' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'extract esms_encours',
                        'defaults' => [
                            'controller' => Controller\ExtractController::class,
                            'action'     => 'esmsencours',
                        ],
                    ],
                ],
                
                /* script-dashboard */
                'script-dashboard-ig-hebdo' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'scriptdashboard ig_hebdo',
                        'defaults' => [
                            'controller' => Controller\ScriptdashboardController::class,
                            'action'     => 'ighebdo',
                        ],
                    ],
                ],
                'script-dashboard-ig-mensuel' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'scriptdashboard ig_mensuel',
                        'defaults' => [
                            'controller' => Controller\ScriptdashboardController::class,
                            'action'     => 'igmensuel',
                        ],
                    ],
                ],
                
                'script-dashboard-esms-ig-hebdo' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'scriptdashboard esms_ig_hebdo',
                        'defaults' => [
                            'controller' => Controller\ScriptdashboardController::class,
                            'action'     => 'esmsigsihebdo',
                        ],
                    ],
                ],
                
                'script-dashboard-esms-ig-mensuel' => [
                    //'type' => Segment::class,
                    'options' => [
                        'route'    => 'scriptdashboard esms_ig_mensuel',
                        'defaults' => [
                            'controller' => Controller\ScriptdashboardController::class,
                            'action'     => 'esmsigsimensuel',
                        ],
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
            'cron' => __DIR__ . '/../view',
        ],
    ],
];
