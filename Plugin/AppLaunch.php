<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Plugin;

use Yong\Magento2DebugBar\Framework\Stand;
use Yong\Magento2DebugBar\Framework\ClassHijacker;
use Yong\Magento2DebugBar\Framework\ObjectManagerStand\Event\Manager;
use Magento\Framework\ObjectManagerInterface;
use Yong\Magento2DebugBar\Framework\Interception\Stand\PluginList;

class AppLaunch {
	public function __construct(ObjectManagerInterface $objetManager) {
		Stand::getInstance()->InitObjectManagerHijacker($objetManager);
	}
	public function beforeLaunch($attacher) {
		if (!($attacher instanceof \Magento\Framework\App\StaticResource)) {
			if (Stand::getInstance()->debugBarEnabled()) {
				ClassHijacker::hijack($attacher, '_eventManager', Manager::class);
				ClassHijacker::hijack($attacher, '_objectManager', Stand::getInstance()->ObjectManager());
			}
		}
	}
	public function aroundcatchException(...$args) {
		if (Stand::getInstance()->debugBarEnabled(true) && class_exists('\Whoops\Run')) {
			$e = $args[3];
			$whoops = new \Whoops\Run;
		    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
		    $whoops->handleException($e);
		    return true;
		} else {
			return $args[0]->___callParent('catchException', [$args[2], $args[3]]);
		}
		return false;
	}
}
