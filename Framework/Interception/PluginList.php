<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Framework\Interception;

use Yong\Magento2DebugBar\Framework\Stand;
use Magento\Framework\Interception\PluginList\PluginList as Base;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\Data\Scoped;
use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Interception\DefinitionInterface;
use Magento\Framework\Interception\PluginListInterface as InterceptionPluginList;
use Magento\Framework\Interception\ObjectManager\ConfigInterface;
use Magento\Framework\ObjectManager\RelationsInterface;
use Magento\Framework\ObjectManager\DefinitionInterface as ClassDefinitions;
use Magento\Framework\ObjectManagerInterface;

class PluginList extends Base {
    private $processed_plugins = null;
    private $in_use = false;

    public function __construct(
        ReaderInterface $reader,
        ScopeInterface $configScope,
        CacheInterface $cache,
        RelationsInterface $relations,
        ConfigInterface $omConfig,
        DefinitionInterface $definitions,
        ObjectManagerInterface $objectManager,
        ClassDefinitions $classDefinitions,
        array $scopePriorityScheme = ['global'],
        $cacheId = 'plugins'
    ) {
        parent::__construct($reader,
            $configScope,
            $cache,
            $relations,
            $omConfig,
            $definitions,
            $objectManager,
            $classDefinitions,
            $scopePriorityScheme,
            $cacheId
        );
        if (Stand::getInstance()->InitObjectManagerHijacker($objectManager)) {
            Stand::getInstance()->applyPlugins($this);
            $this->process_plugins();
        }
    }

    private function process_plugins() {
        $plugins = Stand::getInstance()->getInjectConfig('plugins');
        $this->processed_plugins = [];
        foreach($plugins as $origin => $configs) {
            $this->processed_plugins[$origin] = [];
            foreach($configs['plugins'] as $plugin_name => $plugin_config) {
                foreach($plugin_config['methods'] as $method => $plugin_types) {
                    foreach($plugin_types as $ptype) {
                        $this->processed_plugins[$origin]['___code___'] = [$plugin_name=>$plugin_config['instance']];
                        switch($ptype) {
                            case \Magento\Framework\Interception\DefinitionInterface::LISTENER_BEFORE:
                            case \Magento\Framework\Interception\DefinitionInterface::LISTENER_AFTER:
                                $this->processed_plugins[$origin][$method] = [$ptype=>[$plugin_name]];
                                break;
                            case \Magento\Framework\Interception\DefinitionInterface::LISTENER_AROUND:
                                $this->processed_plugins[$origin][$method] = [$ptype=>$plugin_name];
                            break;
                        }
                    }
                }
            }
        }
        $this->in_use = true;
    }

    public function getNext($type, $method, $code = '__self') {
        $pluginInfo = parent::getNext($type, $method, $code);
        if ($this->in_use) {

        // if (Stand::getInstance()->debugBarEnabled()) {
        //     if (!$this->in_use && is_null($this->processed_plugins)) {
        //         $this->process_plugins();
        //     }
            if (isset($this->processed_plugins[$type]) && isset($this->processed_plugins[$type][$method])) {
                $dynamic_injects = $this->processed_plugins[$type][$method];
                if (is_array($pluginInfo)) {
                    return array_replace($pluginInfo, $dynamic_injects);
                } else {
                    return $dynamic_injects;
                }
            }
        }
        return $pluginInfo;
    }

    public function getPlugin($type, $code) {
        if ($this->in_use) {
            if (isset($this->processed_plugins[$type]['___code___']) && isset($this->processed_plugins[$type]['___code___'][$code])) {
                return $this->_objectManager->get($this->processed_plugins[$type]['___code___'][$code]);
            }
        }
        return parent::getPlugin($type, $code);
    }
}