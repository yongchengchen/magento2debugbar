<?php

namespace Yong\Magento2DebugBar\Framework\App\Router;

Trait BaseTrait
{
    private $moduleFrontName;
    private $actionPath='';

    /**
     * Match module front name
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string $param
     * @return string|null
     */
    protected function matchModuleFrontName(\Magento\Framework\App\RequestInterface $request, $param)
    {
        $this->moduleFrontName = parent::matchModuleFrontName($request, $param);
        return $this->moduleFrontName;
    }

    protected function matchActionPath(\Magento\Framework\App\RequestInterface $request, $param) {
        $this->actionPath = parent::matchActionPath($request, $param);
        return $this->actionPath;
    }

    protected function matchAction(\Magento\Framework\App\RequestInterface $request, array $params)
    {
        $actionInstance = parent::matchAction($request, $params);
        if ($actionInstance === null) {
            $this->addLogToMagento2Debugbar();
        }
        return $actionInstance;
    }

    private $noRouteLogAdded;
    protected function addLogToMagento2Debugbar() {
        if (!$this->noRouteLogAdded) {
            $modules = $this->_routeConfig->getModulesByFrontName($this->moduleFrontName);
            $errorKeyName = sprintf('[%s/%s]404 Not Found', $this->moduleFrontName, $this->actionPath);
            if (count($modules) > 0){
                \Yong\Magento2DebugBar\Block\Magento2Debugbar::getInstance()
                    ->addController($errorKeyName, 
                        [
                            'defined_in'=>$modules,
                            'tried_action_list'=>$this->actionList->getTriedFullPaths()
                        ]);
            } else {
                \Yong\Magento2DebugBar\Block\Magento2Debugbar::getInstance()
                    ->addController($errorKeyName, 
                        [
                            sprintf('frontName [%s] is not defined, please check routes.xml files.Will try it in Url Rewrite.', 
                                $this->moduleFrontName),
                        ]
                    );
            }
            $this->actionList->clearTriedFullPaths();
            $this->noRouteLogAdded = true;
        }
    }

    /**
     * Get not found controller instance
     *
     * @param string $currentModuleName
     * @return \Magento\Framework\App\ActionInterface|null
     */
    protected function getNotFoundAction($currentModuleName)
    {
        $this->addLogToMagento2Debugbar();
        return parent::getNotFoundAction($currentModuleName);
    }
}
