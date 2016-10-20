<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Framework\ObjectManagerStand;

use Yong\Magento2DebugBar\Framework\ClassHijacker;
use Yong\Magento2DebugBar\Framework\ObjectManagerStand\ObjectManager\Config;
use Yong\Magento2DebugBar\Framework\ObjectManagerStand\ObjectManager\Factory;

class ObjectManager extends ClassHijacker {
    public function __construct($original_objectManager) {
        parent::__construct($original_objectManager, null);
        $new_config = $this->hijack($original_objectManager, '_config', Config::class);
        $this->hijack($original_objectManager, '_factory', Factory::class, $new_config);
    }
}
