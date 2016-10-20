<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Framework\View\Layout\File\Collector;

use Magento\Framework\View\Layout\File\Collector\Aggregated as Base;
use Magento\Framework\View\Design\ThemeInterface;

class Aggregated extends Base {
     public function getFiles(ThemeInterface $theme, $filePath) {
        return parent::getFiles($theme, $filePath);
     }
}