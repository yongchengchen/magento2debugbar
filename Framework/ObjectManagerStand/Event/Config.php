<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Framework\ObjectManagerStand\Event;

use Yong\Magento2DebugBar\Framework\Stand;
use Yong\Magento2DebugBar\Framework\ClassHijacker;

class Config extends ClassHijacker {
    private $_caches = [];

    private function findExtraObservers($eventName, $observers) {
        static $events;
        if (empty($events)) {
            $events = Stand::getInstance()->getInjectConfig('events');
        }
        $extra = isset($events[$eventName]) ? $events[$eventName] : FALSE;

        if ($extra !== FALSE) {
            $observers = array_merge_recursive($observers, $extra);
        }

        if (isset($events['*'])) {
            $observers['*'] = $events['*'];
        }
        return $observers;
    }

    public function getObservers($eventName) {
        Stand::getInstance()->tick();
        $cache = isset($this->_caches[$eventName]) ? $this->_caches[$eventName] : FALSE;
        if (FALSE !== $cache) {
            return $cache;
        }

        $observers = $this->_core->getObservers($eventName);
        $extra = $this->findExtraObservers($eventName, $observers);
        if ($extra !== FALSE) {
            if (!empty($extra) && count($extra) > 0) {
                $this->_caches[$eventName] = $extra;
                return $extra;
            }
        }
        return $observers;
    }
}
