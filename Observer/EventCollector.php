<?php 
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Observer;

use Yong\Magento2DebugBar\Block\Magento2Debugbar;
use Yong\Magento2DebugBar\Framework\Stand;
use Magento\Framework\Event\ObserverInterface;

class EventCollector implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $eventName = $event->getName();
        $starttime = Stand::getInstance()->tick();

        Magento2Debugbar::getInstance()->addEvent($eventName, $starttime, 
            microtime(true), 
            $event->toArray()
        );
        return $this;
    }
}
