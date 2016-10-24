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
        if (Stand::getInstance()->debugBarEnabled(true)) {
            $op = $this->_request->getParam('op');
            switch(strtolower($op)) {
                case 'get':
                    return $this->loadProfile();
                    break;
                case 'debughint':
                    return $this->setDebughint();
                    break;
                default:
                    break;
            }
        }
        $this->getResponse()->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_FORBIDDEN);
        return $this->getResponse()->setBody('<center><h1>403 Forbidden</h1></center>');
    }

    /**
     * Loads a profile.
     *
     * @return     Magento\Framework\Controller\Result\Json  ( description_of_the_return_value )
     */
    private function loadProfile() {
        $id = $this->_request->getParam('id');
        $json_result = $this->_objectManager->get('Magento\Framework\Controller\Result\JsonFactory')->create();
        $storage = new FilesystemStorage($this->_objectManager->get('Magento\Framework\Filesystem'));
        return $json_result->setData($storage->get($id));
    }

    /**
     * enable or disable debughint
     *
     * @param      <type>  $id     The identifier
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function setDebughint() {
        $action = $this->_request->getParam('action');
        $hintname = $this->_request->getParam('hintname');
        $resultFactory = $this->_objectManager->get('Magento\Framework\Controller\ResultFactory');
        $resultRedirect = $resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        $cookieManager = $this->_objectManager->get('Magento\Framework\Stdlib\CookieManagerInterface');
        $cookieMetadataFactory = $this->_objectManager->get('Magento\Framework\Stdlib\Cookie\CookieMetadataFactory');
        $sessionManager = $this->_objectManager->get('Magento\Framework\Session\SessionManagerInterface');

        $metadata = $cookieMetadataFactory
              ->createPublicCookieMetadata()
              ->setPath('/')
              ->setDuration(864000)
              ->setDomain($sessionManager->getCookieDomain());

        switch($action) {
            case 'enable':
                $cookieManager->setPublicCookie(
                    $hintname,
                    'true',
                    $metadata
                );
                break;
            case 'disable':
                $cookieManager->deleteCookie(
                    $hintname,
                    $metadata
                );
                break;
        }

        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}