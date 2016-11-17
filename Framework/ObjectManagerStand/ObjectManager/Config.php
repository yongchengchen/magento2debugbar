<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Framework\ObjectManagerStand\ObjectManager;

use Yong\Magento2DebugBar\Framework\Stand;
use Yong\Magento2DebugBar\Framework\ClassHijacker;
use Magento\Framework\App\FrontController;

class Config extends ClassHijacker {
    private $overrides;

    private function getOverride($type) {
        if (empty($this->overrides)) {
            $config = Stand::getInstance()->getInjectConfig();
            if (isset($config['overrides'])) {
                $this->overrides = $config['overrides'];
                if(!isset($this->overrides['arguments_from'])) {
                    $this->overrides['arguments_from'] = [];
                }
            } else {
                $this->overrides = ['preferences'=>[], 'types'=>[], 'arguments_from'=>[]];
            }
        }

        $alternative = isset($this->overrides['preferences'][$type]) ? $this->overrides['preferences'][$type] : $type;
        $this->getOverrideArguments($alternative, $type);
        return $alternative;
    }

    private function getOverrideArguments($override_type, $original_type = false) {
        if (isset($this->overrides['arguments_from'][$override_type])) {
            $result = $this->overrides['arguments_from'][$override_type];
            if ($original_type && $result != $original_type) {
                $this->overrides['arguments_from'][$override_type] = $original_type;
            }
            return $this->overrides['arguments_from'][$override_type];
        }

        if ($original_type && $override_type != $original_type) {
            $this->overrides['arguments_from'][$override_type] = $original_type;
        }
        return $override_type;
    }

    /**
     * Retrieve list of arguments per type
     *
     * @param string $type
     * @return array
     */
    public function getArguments($type)
    {
        if (isset($this->overrides['types'][$type])) {
            return $this->overrides['types'][$type];
        }
        $type = $this->getOverrideArguments($type);
        return $this->_core->getArguments($type);
    }
    
    /**
     * Retrieve instance type
     *
     * @param string $instanceName
     * @return mixed
     */
    public function getInstanceType($instanceName)
    {
        $pref = $this->getPreference($instanceName);
        return $this->_core->getInstanceType($pref);
    }

    /**
     * Retrieve preference for type
     *
     * @param string $type
     * @return string
     * @throws \LogicException
     */
    public function getPreference($type)
    {
        $pref = $this->getOverride($type);
        if ($pref != $type) {
            return $pref;
        }
        $pref = $this->_core->getPreference($type);
        return $this->getOverride($pref);
    }
}
