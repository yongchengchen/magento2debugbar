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

use Yong\Magento2DebugBar\Framework\ClassHijacker;

class Factory extends ClassHijacker {
    public function __construct($factory, $new_config){
        $pro = $this->hijack($factory, 'config', $new_config);
        parent::__construct($factory);
    }
}