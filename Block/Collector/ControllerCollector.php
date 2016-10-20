<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Block\Collector;

use DebugBar\DataCollector\TimeDataCollector;

class ControllerCollector extends TimeDataCollector
{
    public function addController($name, $params)
    {
        $time = microtime(true);
        $this->addMeasure($name, $time, $time, $params);
    }

    public function collect()
    {
        $data = parent::collect();
        $data['nb_measures'] = count($data['measures']);

        return $data;
    }

    public function getName()
    {
        return 'controller';
    }

    public function getWidgets()
    {
        return array(
          "controllers" => array(
            "icon" => "tasks",
            "widget" => "PhpDebugBar.Widgets.TimelineWidget",
            "map" => "controller",
            "default" => "{}",
          ),
          'controllers:badge' => array(
            'map' => 'controller.nb_measures',
            'default' => 0,
          ),
        );
    }
}
