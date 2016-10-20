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

class FrontController
{
    private $hacked = false;
    public function beforeDispatch(\Magento\Framework\App\FrontController $attacher) {
        if (!$this->hacked && Stand::getInstance()->debugBarEnabled()) {
            ClassHijacker::hijack($attacher, '_routerList', \Yong\Magento2DebugBar\Framework\App\RouterListHijacker::class);
            $this->hacked = true;
        }
    }
}