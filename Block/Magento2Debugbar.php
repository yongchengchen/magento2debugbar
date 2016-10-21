<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Block;

use DebugBar\StandardDebugBar;
use Yong\Magento2DebugBar\Framework\Stand;
use Yong\Magento2DebugBar\Block\Collector\QueryCollector;
use Yong\Magento2DebugBar\Block\Collector\TemplateCollector;
use Yong\Magento2DebugBar\Block\Collector\ModelCollector;
use Yong\Magento2DebugBar\Block\Collector\ProfilerCollector;
use Yong\Magento2DebugBar\Block\Collector\ControllerCollector;
use Yong\Magento2DebugBar\Model\FilesystemStorage;

class Magento2Debugbar extends StandardDebugBar{
    CONST OPENHANDLER_URL = '/phpdebugbar/openhandler/load';

    private $queryCollector;
    private $templateCollector;
    private $modelCollector;
    private $profilerCollector;
    private $controllerCollector;

    public static function getInstance() {
        static $self;
        if (empty($self)) {
            $self = new self();
            $self->init();
        }
        return $self;
    }

    private function init() {
        $this->setStorage(new FilesystemStorage(Stand::getInstance()->ObjectManager()->get('Magento\Framework\Filesystem')));

        $this->queryCollector = new QueryCollector();
        $this->addCollector($this->queryCollector);

        $this->templateCollector = new TemplateCollector();
        $this->addCollector($this->templateCollector);

        $this->modelCollector = new \Yong\Magento2DebugBar\Block\Collector\ModelCollector();
        $this->addCollector($this->modelCollector);

        $this->profilerCollector = new ProfilerCollector();
        $this->addCollector($this->profilerCollector);

        $this->controllerCollector = new ControllerCollector();
        $this->addCollector($this->controllerCollector);

        \Magento\Framework\Profiler::add($this->profilerCollector);
        \Magento\Framework\Profiler::start('magento');
        \Magento\Framework\Profiler::start('store.resolve');
    }

    public function addMessage($msg) {
        $this->debugbar['messages']->addMessage($msg);
    }

    public function addQuery($query, $bindings, $time, $connection) {
        $this->queryCollector->addQuery($query, $bindings, $time, $connection);
    }

    public function addView($block, $fileName, $dictionary) {
        $this->templateCollector->addView($block, $fileName, $dictionary);
    }

    public function addModel($name, $params) {
        $this->modelCollector->addModel($name, $params);
    }

    public function addController($name, $params) {
        $this->controllerCollector->addController($name, $params);
    }

    /**
     * output phpdebugbar assets
     *
     * @param      \Magento\Framework\App\ResponseInterface  $response  The response
     */
    public function modifyResponse(\Magento\Framework\App\ResponseInterface $response) {
        if ($response->isRedirect()) {
            $this->stackData();
            return;
        }

        $isAjax = Stand::getInstance()->ObjectManager()->get('Magento\Framework\App\RequestInterface')->isAjax();
        if ($isAjax || $response instanceof \Magento\Framework\Controller\Result\JsonFactory) {
            $this->setHttpDriver(new \Yong\Magento2DebugBar\Model\HttpDriver($response));
            $this->sendDataInHeaders(true);
            return;
        }

        $renderer = $this->getJavascriptRenderer();
        if ($this->getStorage()) {
            $renderer->setOpenHandlerUrl(self::OPENHANDLER_URL);
        }
        $renderer->setIncludeVendors(false);
        $renderer->setBindAjaxHandlerToXHR(true);

        $append = sprintf('<script type="text/javascript" src="%s"></script>
            %s %s',
            Stand::getInstance()->getViewFileUrl('jquery.js', [], true),
            $renderer->renderHead(),
            $renderer->render()
        );
        $response->appendBody($append);
    }
}