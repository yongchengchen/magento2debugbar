<?php
/**
 * Plugin for the template engine factory that makes a decision of whether to activate debugging hints or not
 */
namespace Yong\Magento2DebugBar\Plugin;

use Magento\Framework\View\TemplateEngineFactory;
use Magento\Framework\View\TemplateEngineInterface;
use Magento\Store\Model\ScopeInterface;
use Yong\Magento2DebugBar\Framework\Stand;

class DebugHints {
    const TEMPLATE_HINT = '_templatehints_';
    const BLOCK_HINT = '_blockhints_';

    /**
     * Wrap template engine instance with the debugging hints decorator, depending of the store configuration
     *
     * @param TemplateEngineFactory $subject
     * @param TemplateEngineInterface $invocationResult
     *
     * @return TemplateEngineInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreate(
        TemplateEngineFactory $subject,
        TemplateEngineInterface $invocationResult
    ) {
        $stand = Stand::getInstance();
        $cookieManager = $stand->ObjectManager()->get('Magento\Framework\Stdlib\CookieManagerInterface');

        if ($stand->debugBarEnabled()) {
            $showtemplateHints = ('true' == $cookieManager->getCookie(self::TEMPLATE_HINT));
            if ($showtemplateHints) {
                $debugHintsFactory = $stand->ObjectManager()->get('Magento\Developer\Model\TemplateEngine\Decorator\DebugHintsFactory');
                return $debugHintsFactory->create([
                    'subject' => $invocationResult,
                    'showBlockHints' => ('true' == $cookieManager->getCookie(self::BLOCK_HINT))
                ]);
            }
        }

        return $invocationResult;
    }
}
