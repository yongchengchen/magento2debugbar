<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Model;

use DebugBar\HttpDriverInterface;

class HttpDriver implements HttpDriverInterface
{
    private $response;
    public function __construct($response) {
        $this->response = $response;
    }

    public function setHeaders(array $headers)
    {
        if (!is_null($this->response)) {
            $this->response->getHeaders()->addHeaders($headers);
        }
    }

    public function isSessionStarted()
    {
        return true;
    }

    public function setSessionValue($name, $value)
    {
        
    }

    public function hasSessionValue($name)
    {
        return false;
    }

    public function getSessionValue($name)
    {
        return null;
    }

    public function deleteSessionValue($name)
    {
        
    }
}