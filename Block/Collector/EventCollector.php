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
use Yong\Magento2DebugBar\Block\ValueExporter;

class EventCollector extends TimeDataCollector
{
    /** @var Dispatcher */
    protected $events;

    /** @var ValueExporter */
    protected $exporter;

    public function __construct()
    {
        parent::__construct();
        $this->exporter = new ValueExporter();
    }

    public function addEvent($name, $starttime, $endtime, $data)
    {
        $params = [];
        foreach ($data as $key => $value) {
            $params[$key] = $this->exporter->exportValue($value);
        }

        $this->addMeasure($name, $starttime, $endtime, $params);
    }

    public function collect()
    {
        $data = parent::collect();
        $data['nb_measures'] = count($data['measures']);

        return $data;
    }

    public function getName()
    {
        return 'event';
    }

    public function getWidgets()
    {
        return array(
          "events" => array(
            "icon" => "tasks",
            "widget" => "PhpDebugBar.Widgets.TimelineWidget",
            "map" => "event",
            "default" => "{}",
          ),
          'events:badge' => array(
            'map' => 'event.nb_measures',
            'default' => 0,
          ),
        );
    }
}
