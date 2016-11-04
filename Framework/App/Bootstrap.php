<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Framework\App;

use Yong\Magento2DebugBar\Framework\ClassHijacker;
use Yong\Magento2DebugBar\Framework\Stand;

class Bootstrap extends ClassHijacker
{
    public function createApplication($type, $arguments = [])
    {
        $objectManager         = $this->getObjectManager();
        if (Stand::getInstance()->InitObjectManagerHijacker($objectManager)) {
            $this->hijack($this->_core, 'objectManager', Stand::getInstance()->ObjectManager());
        }
        return $this->_core->createApplication($type, $arguments);
    }
}