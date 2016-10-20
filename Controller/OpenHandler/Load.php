<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Controller\OpenHandler;

use Yong\Magento2DebugBar\Framework\Stand;
use Yong\Magento2DebugBar\Model\FilesystemStorage;

class Load extends \Magento\Framework\App\Action\Action
{
    public function __construct(\Magento\Framework\ObjectManagerInterface $om) {
        parent::__construct($om->get('Magento\Framework\App\Action\Context'));
    }
    
    /**
    * Default customer account page
    *
    * @return void
    */
    public function execute()
    {
        if (!Stand::getInstance()->debugBarEnabled(true)) {
            $this->getResponse()->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_FORBIDDEN);
            return $this->getResponse()->setBody('<center><h1>403 Forbidden</h1></center>');
        }

        $json_result = $this->_objectManager->get('Magento\Framework\Controller\Result\JsonFactory')->create();
        $id = $this->_request->getParam('id');
        $op = $this->_request->getParam('op');
        if ($op !== 'get') {
            return $json_result->setData(['error'=>'Wrong operation']);
        }

        $storage = new FilesystemStorage($this->_objectManager->get('Magento\Framework\Filesystem'));
        return $json_result->setData($storage->get($id));
    }
}