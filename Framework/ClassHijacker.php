<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Framework;

use Closure;

class ClassHijacker
{
    protected $_core;
    protected static $binding_closures = [];
    public function __construct($original, $extra_param = null)
    {
        $this->_core = $original;
    }

    public function __call($method, $args)
    {
        if (method_exists($this->_core, $method)) {
            return call_user_func_array(array($this->_core, $method), $args);
        }
    }

    public static function injectPropertyAccessor($target)
    {
        $class_name = get_class($target);
        if (!isset(self::$binding_closures[$class_name])) {
            $accessor = function ($target, $property_name, $new_property = null) {
                if ($new_property !== null) {
                    $target->$property_name = $new_property;
                }
                return $target->$property_name;
            };

            self::$binding_closures[$class_name] = Closure::bind($accessor, null, $class_name);
        }
        return self::$binding_closures[$class_name];
    }

    /**
     * hijack into a object
     *
     * @param      object    $target              The target
     * @param      string    $property_name       The property name of the target
     * @param      mixed     $hijacker_name        The hijacker class name
     */
    public static function hijack($target, $property_name, $hijacker_name, $extra_param = null)
    {
        $new_property = $hijacker_name;
        $accessor     = self::injectPropertyAccessor($target);
        if (is_string($hijacker_name)) {
            $new_property = new $hijacker_name($accessor($target, $property_name), $extra_param);
        }
        if (!is_object($new_property)) {
            throw new \Exception("hijacker_name should be a class name or object or null", 1);
        }
        return $accessor($target, $property_name, $new_property);
    }

    public function getOriginal()
    {
        return $this->_core;
    }
}
