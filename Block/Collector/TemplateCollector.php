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

class TemplateCollector extends RequestDataCollector {
    protected $templates = array();
    protected $collect_data;

    public function __construct($collectData = true)
    {
        $this->collect_data = $collectData;
        $this->name = 'templates';
        $this->templates = array();
    }

    /**
     * Add a View instance to the Collector
     *
     * @param \Illuminate\View\View $view
     */
    public function addView($block, $fileName, $dictionary)
    {
        if ($block instanceof \Magento\Framework\View\Element\AbstractBlock) {
	    $block_name = $block->getNameInLayout();
            $dictionary['class'] = get_class($block);
            $dictionary['template'] = $fileName;
            $dictionary['children'] = $block->getChildNames();
            $this->templates[$block_name] = $this->getDataFormatter()->formatVar($dictionary);
        }
    }

    public function collect()
    {
        $this->templates['total_amount'] = count(array_keys($this->templates));
        return $this->templates;
    }

    public function getName()
    {
        return 'templates';
    }

    public function getWidgets()
    {
        return array(
            'templates' => array(
                'icon' => 'leaf',
                'widget' => 'PhpDebugBar.Widgets.VariableListWidget',
                'map' => 'templates',
                'default' => '[]'
            ),
            'templates:badge' => array(
                'map' => 'templates.total_amount',
                'default' => 0,
             ),
        );
    }
}
