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

use Magento\Framework\ObjectManagerInterface;
use Yong\Magento2DebugBar\Framework\ObjectManagerStand\ObjectManager;

class Stand
{
    private $timestamp;
    private $inject_config;
    protected $_objectManager;

    private function enable_checker_cookie($pair)
    {
        if (isset($pair['name'])
            && isset($pair['value'])
            && isset($__COOKIE[$pair['name']])
            && $__COOKIE[$pair['name']] === $pair['value']) {
            return true;
        }
        return false;
    }

    /**
     * Turn on debugbar.
     *
     * @var        boolean
     */
    private $debugbar_enabled = false;
    /**
     * if debugbar is enabled, check collect if is suppressed
     *
     * @var        boolean
     */
    private $debugbar_collect_suppressed = false;

    public static function getInstance()
    {
        static $self;
        if (empty($self)) {
            $self = new self();
        }
        return $self;
    }

    private function __construct()
    {
        $this->registerErrorHandler();
        $this->timestamp = microtime(true);
    }

    /**
     * init object manager hijacker
     *
     * @param      Magento\Framework\ObjectManagerInterface  $objectManager
     *
     * @return     Yong\Magento2DebugBar\Framework\ObjectManagerStand\ObjectManager
     */
    public function InitObjectManagerHijacker(ObjectManagerInterface $objectManager = null)
    {
        if (!empty($objectManager)) {
            if (empty($this->_objectManager)) {
                $this->initConfig($objectManager);
                if ($this->debugbar_enabled && !$this->debugbar_collect_suppressed) {
                    $this->_objectManager = new ObjectManager($objectManager);
                } else {
                    $this->_objectManager = $objectManager;
                }
            }
        }
        return (!empty($this->_objectManager) && ($this->_objectManager instanceof ObjectManager));
    }

    public function ObjectManager()
    {
        return $this->_objectManager;
    }

    /**
     * init config from etc/config.php
     *
     * @param      \Magento\Framework\ObjectManagerInterface  $objectManager  The object manager
     */
    private function initConfig(ObjectManagerInterface $objectManager)
    {
        //so far does not support cli debug
        if (PHP_SAPI === 'cli') {
            return;
        }

        $envconfig     = $objectManager->get(\Magento\Framework\App\DeploymentConfig::class);
        $debugbar_conf = $envconfig->get('phpdebugbar');
        //check global swtich, if false, return false;
        if (empty($debugbar_conf) || !isset($debugbar_conf['enabled']) || !$debugbar_conf['enabled']) {
            return;
        }

        //further check
        if (isset($debugbar_conf['enable_checker'])) {
            foreach ($debugbar_conf['enable_checker'] as $key => $pair) {
                $func_name = 'enable_checker_' . $key;
                if (method_exists($this, $func_name)) {
                    $this->debugbar_enabled = $this->$func_name($pair);
                } else {
                    $this->debugbar_enabled = false;
                }
                if (!$this->debugbar_enabled) {
                    return;
                }
            }
        }

        //if is enabled, check for openhandler controller
        if ($this->debugbar_enabled) {
            $url = parse_url($_SERVER['REQUEST_URI']);
            if ($url['path'] == \Yong\Magento2DebugBar\Block\Magento2Debugbar::OPENHANDLER_URL) {
                $this->debugbar_enabled            = false;
                $this->debugbar_collect_suppressed = true;
                return;
            }
            $this->inject_config = require_once __DIR__ . '/../etc/config.php';
        }
    }

    /**
     * get dynamic inject config key=>value sets
     *
     * @param      string  $key    The key
     *
     * @return     array  The configuration.
     */
    public function getInjectConfig($key = null)
    {
        if (empty($key)) {
            return $this->inject_config;
        }

        return isset($this->inject_config[$key]) ? $this->inject_config[$key] : [];
    }

    public function debugBarEnabled($check_collect_suppressed = false)
    {
        return $check_collect_suppressed ? $this->debugbar_collect_suppressed : $this->debugbar_enabled;
    }

    /**
     * dynamic apply plugins configuration to pluginlist
     *
     * @param      \Magento\Framework\Interception\PluginList\PluginList  $pluginlist  The pluginlist
     */
    public function applyPlugins(\Magento\Framework\Interception\PluginList\PluginList $pluginlist)
    {
        if (isset($this->config['plugins']) && count($this->config['plugins']) > 0) {
            $pluginlist->getNext('Yong_magento2debugbar', 'trigger_load_scope_data');
            $pluginlist->merge($this->config['plugins']);
        }
    }

    /**
     * tick current timestamp and return previous timestamp
     *
     * @return     timestamp
     */
    public function tick()
    {
        $oldtimestamp    = $this->timestamp;
        $this->timestamp = microtime(true);
        return $oldtimestamp;
    }

    /**
     * register Whoops Error Handler
     */
    private function registerErrorHandler()
    {
        if (class_exists('\Whoops\Run')) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        }
    }

    /**
     * Retrieve url of a view file
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getViewFileUrl($fileId, array $params = [], $is_secure = false)
    {
        try {
            $params = array_merge(['_secure' => $is_secure], $params);
            $url    = $this->_objectManager->get('Magento\Framework\View\Asset\Repository')->getUrlWithParams($fileId, $params);
            $parts  = explode('::', $fileId);
            if (count($parts) == 2) {
                $path_part       = '/' . str_replace('_', '/', $parts[0]) . '/';
                $modulename_part = '/' . $parts[0] . '/';
                if (stripos($url, $path_part) > 0 && strpos($url, $modulename_part) > 0) {
                    return str_replace($modulename_part, '/', $url);
                }
            }
            return $url;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->critical($e);
            return $this->_getNotFoundUrl();
        }
    }

    public function isAdmin(Magento\Framework\ObjectManagerInterface $objectManager = null)
    {
        $objectManager = empty($objectManager) ? $this->_objectManager : $objectManager;
        return $objectManager->get('Magento\Framework\App\State')->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML;
    }
}
