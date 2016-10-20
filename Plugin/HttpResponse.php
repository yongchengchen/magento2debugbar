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
use Yong\Magento2DebugBar\Block\Magento2Debugbar;
use Magento\Framework\App\ResponseInterface;

class HttpResponse {
	public function beforeSendResponse(ResponseInterface $response) {
        if (Stand::getInstance()->debugBarEnabled()) {
    		Magento2Debugbar::getInstance()->modifyResponse($response);
        }
	}
}