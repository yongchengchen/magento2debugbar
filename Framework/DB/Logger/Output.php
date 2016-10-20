<?php

/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */

namespace Yong\Magento2DebugBar\Framework\DB\Logger;
use Yong\Magento2DebugBar\Block\Magento2Debugbar;

class Output extends \Magento\Framework\DB\Logger\Quiet
{
    private $timer;

    public function startTimer()
    {
        $this->timer = microtime(true);
    }

    public function logStats($type, $sql, $bind = [], $result = null)
    {
        Magento2Debugbar::getInstance()->addQuery($sql, $bind, $this->timer, $type);
    }
}
