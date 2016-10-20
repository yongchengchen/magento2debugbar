<?php 
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Observer;

use Yong\Magento2DebugBar\Block\Magento2Debugbar;
use Magento\Framework\Event\ObserverInterface;

class ModelCollector implements ObserverInterface
{
    public $models = array();
    public $collections = array();
    public $actions = array();
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $object = $event->getObject();
        $key = get_class($object);

        if( array_key_exists($key, $this->models) ) {
            $this->models[$key]['occurences']++; 
        } else {
            $model = array();
            $model['class'] = $key;
            $model['resource_name'] = $object->getResourceName();
            $model['occurences'] = 1;
            Magento2Debugbar::getInstance()->addModel($key, $model);

            $this->models[$key] = $model;
        }

        return $this;
    }
}