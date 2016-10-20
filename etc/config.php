<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
return [ 
    'overrides' => 
    [
        'preferences'=> [
            'Magento\Framework\View\TemplateEngine\Php' =>  'Yong\Magento2DebugBar\Framework\View\TemplateEngine\Php',
            'Magento\Framework\View\Layout\File\Collector\Aggregated' =>'Yong\Magento2DebugBar\Framework\View\Layout\File\Collector\Aggregated',
            'Magento\Framework\DB\Logger\Quiet' => 'Yong\Magento2DebugBar\Framework\DB\Logger\Output',
        ],
        'types'=>[
            
        ]
    ],
    
    'plugins' => 
    [
        'Magento\Framework\App\Response\Http' => 
        [
            'plugins' => [
                'debugbarresponse'=>[
                    'instance' => 'Yong\Magento2DebugBar\Plugin\HttpResponse',
                    'sortOrder' => 20,
                    'methods' => [
                        'sendResponse'=>[1]
                        ]
                ]
            ]
        ],
        'Magento\Framework\App\FrontController' =>
        [
            'plugins' => [
                'routerlisthack'=>[
                    'instance' => 'Yong\Magento2DebugBar\Plugin\FrontController',
                    'sortOrder' => 5,
                    'methods' => [
                        'dispatch'=>[1]
                        ]
                ]
            ]
        ],
    ],

    'events' => 
    [
        'model_load_after' => 
        [
            'model_load_after_4_dbg'=>
            [
                'name'=>"model_load_after_4_dbg",
                'instance'=>"Yong\Magento2DebugBar\Observer\ModelCollector"
            ]
        ],

        // '*' => [
        //     'name'=>"phpdebug_event_collector",
        //     'instance'=>"Yong\Magento2DebugBar\Observer\EventCollector"
        // ],
    ],
];