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
use DebugBar\Bridge\Twig\TwigCollector;
use Yong\Magento2DebugBar\Block\ValueExporter;

class TemplateCollector extends TwigCollector {
	protected $templates = array();
    protected $collect_data;

    public function __construct($collectData = true)
    {
        $this->collect_data = $collectData;
        $this->name = 'views';
        $this->templates = array();
        $this->exporter = new ValueExporter();
    }

    /**
     * Add a View instance to the Collector
     *
     * @param \Illuminate\View\View $view
     */
    public function addView($block, $fileName, $dictionary)
    {
    	$params = array();
        foreach ($dictionary as $key => $value) {
            $params[$key] = $this->exporter->exportValue($value);
        }

        $params['block'] = get_class($block);

        $this->templates[] = array(
            'name' => $fileName,
            'param_count' => count($params),
            'params' => $params,
            'type' => 'php',
        );
    }

    public function collect()
    {
        $templates = $this->templates;

        return array(
            'nb_templates' => count($templates),
            'templates' => $templates,
        );
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
                'widget' => 'PhpDebugBar.Widgets.TemplatesWidget',
                'map' => 'templates',
                'default' => '[]'
            ),
            'templates:badge' => array(
                'map' => 'templates.nb_templates',
                'default' => 0
            )
        );
    }
}
