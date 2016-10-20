<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Framework\App;

use Yong\Magento2DebugBar\Framework\ClassHijacker;
use Yong\Magento2DebugBar\Block\Magento2Debugbar;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterListInterface;

class RouterListHijacker extends ClassHijacker implements RouterListInterface {
    private $_current;
    public function match(RequestInterface $request) {
        $actionInstance = $this->_current->match($request);
        if ($actionInstance) {
            Magento2Debugbar::getInstance()->addController(
                get_class($actionInstance), 
                [
                    'module'=>$request->getModuleName(),
                    'action'=>$request->getActionName()
                ]
            );
        }
        return $actionInstance;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return RouterInterface
     */
    public function current()
    {
        $this->_current = $this->_core->current();
        return $this;
    }

    public function next(){
        return $this->_core->next();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return string|int|null
     */
    public function key()
    {
        return $this->_core->key();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->_core->valid();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->_core->rewind();
    }
}