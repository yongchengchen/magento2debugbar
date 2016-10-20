<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Framework\View\TemplateEngine;

use Magento\Framework\View\TemplateEngine\Php as Base;
use Yong\Magento2DebugBar\Block\Magento2Debugbar;
use Magento\Framework\View\Element\BlockInterface;

class Php extends Base {
    public function render(BlockInterface $block, $fileName, array $dictionary = [])
    {
        Magento2Debugbar::getInstance()->addView($block, $fileName, $dictionary);
        return parent::render($block, $fileName, $dictionary);
    }
}
