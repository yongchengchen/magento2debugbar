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

use DebugBar\DataCollector\RequestDataCollector;

class LayoutCollector extends RequestDataCollector {
    protected $layouts = array();
    protected $collect_data;

    public function __construct($collectData = true)
    {
        $this->collect_data = $collectData;
        $this->name = 'layouts';
        $this->layouts = array();
    }

    /**
     * Add a Layout to the Collector
     */
    public function addLayout($handleName, $fileName)
    {
    	$this->layouts[$handleName] = $this->getDataFormatter()->formatVar($fileName);
    }

    public function collect()
    {
        $this->layouts['total_amount'] = count(array_keys($this->layouts));
        return $this->layouts;
    }

    public function getName()
    {
        return 'layouts';
    }

    public function getWidgets()
    {
        return array(
            'layouts' => array(
                'icon' => 'leaf',
                'widget' => 'PhpDebugBar.Widgets.VariableListWidget',
                'map' => 'layouts',
                'default' => '[]'
            ),
            'layouts:badge' => array(
                'map' => 'layouts.total_amount',
                'default' => 0,
             ),
        );
    }
}
