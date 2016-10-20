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

use Yong\Magento2DebugBar\Framework\ClassHijacker;

class Manager extends ClassHijacker {
    public function __construct($eventManager, $extra_param) {
        parent::__construct($eventManager);
        $this->hijack($eventManager, '_eventConfig', Config::class);
    }
}
