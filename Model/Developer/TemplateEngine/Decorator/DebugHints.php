<?php

namespace Yong\Magento2DebugBar\Model\Developer\TemplateEngine\Decorator;

use Magento\Developer\Model\TemplateEngine\Decorator\DebugHints as Base;

class DebugHints extends Base
{
    /**
     * Insert block debugging hints into the rendered block contents
     *
     * @param string $blockHtml
     * @param \Magento\Framework\View\Element\BlockInterface $block
     * @return string
     */
    protected function _renderBlockHints($blockHtml, \Magento\Framework\View\Element\BlockInterface $block)
    {
        $blockClass = get_class($block);
        $blockName = $block->getNameInLayout();
        return <<<HTML
<div class="debugging-hint-block-class" style="position: absolute; top: 0; padding: 2px 5px; font: normal 11px Arial; background: red; right: 0; color: blue; white-space: nowrap;" onmouseover="this.style.zIndex = 999;" onmouseout="this.style.zIndex = 'auto';" title="{$blockClass}">
[{$blockName}]{$blockClass}</div>
{$blockHtml}
HTML;
    }
}
