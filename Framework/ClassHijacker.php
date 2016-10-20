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

class ClassHijacker {
	protected $_core;
	public function __construct($original, $extra_param=null){
	        $this->_core = $original;
	}

	public function __call($method, $args)
	{
	    if (method_exists($this->_core, $method)) {
	            return call_user_func_array(array($this->_core, $method), $args);
	    }
	}

	/**
	 * hijack into a object
	 *
	 * @param      object    $target              The target
	 * @param      string    $property_name       The property name of the target
	 * @param      mixed     $hijacker_name  	  The hijacker class name
	 */
	public static function hijack($target, $property_name, $hijacker_name, $extra_param=null) {
			$reflection = new \ReflectionClass($target);
			if (!$reflection->hasProperty($property_name)) {
				return null;
			}
			
			$property = $reflection->getProperty($property_name);
			$property->setAccessible(true);

			$new_property = $hijacker_name;
			if (is_string($hijacker_name)) {
				$new_property = new $hijacker_name($property->getValue($target), $extra_param);
			} 
			if (!is_object($new_property) && !is_array($new_property)) {
				throw new \Exception("hijacker_name should be a class name or object or null", 1);
			}
			$property->setValue($target, $new_property);
			return $new_property;
	}

	public static function retrive_property($target, $property_name) {
		$reflection = new \ReflectionClass($target);
		$property = $reflection->getProperty($property_name);
		$property->setAccessible(true);
		return $property->getValue($target);
	}

	public function getOriginal() {
		return $this->_core;
	}
}
