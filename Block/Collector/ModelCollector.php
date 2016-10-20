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

class ModelCollector extends TimeDataCollector
{
    /** @var Dispatcher */
    protected $model;

    public function addModel($name, $params)
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
        return 'model';
    }

    public function getWidgets()
    {
        return array(
          "models" => array(
            "icon" => "tasks",
            "widget" => "PhpDebugBar.Widgets.TimelineWidget",
            "map" => "model",
            "default" => "{}",
          ),
          'models:badge' => array(
            'map' => 'model.nb_measures',
            'default' => 0,
          ),
        );
    }
}
