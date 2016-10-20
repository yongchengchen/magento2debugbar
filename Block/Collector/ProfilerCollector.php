<?php
/*
 * This file is part of the Magento2DebugBar package.
 *
 * (c) Yongcheng Chen <yongcheng.chen@live.com>
 *
 * For the full copyright and license information, please view the README.md
 * file that was distributed with this source code.
 */
namespace Yong\Magento2DebugBar\Block\Collector;

use DebugBar\DataCollector\TimeDataCollector;
use Magento\Framework\Profiler\DriverInterface;
use Magento\Framework\Profiler\Driver\Standard\Stat;

class ProfilerCollector extends TimeDataCollector implements DriverInterface
{
    private $_stat;
    private $tabcount;
    private $levels;
    public function __construct() {
       $this->_stat = new Stat();
       $this->tabcount = 1;
	   $this->levels = [];
    }

    public function collect()
    {
        $timers =  $this->_stat->getFilteredTimerIds();
        $time = microtime(true);

        foreach ($timers as $timerId) {
            $params = $this->_stat->get($timerId);
            $name =  $timerId;
            if ($this->levels[$timerId] > 1) {
                $name = str_repeat('  ', $this->levels[$timerId] -1 ) . 'L' . ($this->levels[$timerId]-1) . '--' . $timerId;
            } 
            $this->addMeasure($name,  $time - $params[Stat::TIME], $time, 
                [
                    'REALMEM'=>$params[Stat::REALMEM], 
                    'EMALLOC'=>$params[Stat::EMALLOC],
                    'COUNT'=>$params[Stat::COUNT]
                ]);
        }
        $data = parent::collect();
        $data['nb_measures'] = count($data['measures']);

        return $data;
    }

    public function getName()
    {
        return 'profiler';
    }

    public function getWidgets()
    {
        return array(
          "profilers" => array(
            "icon" => "tasks",
            "widget" => "PhpDebugBar.Widgets.TimelineWidget",
            "map" => "profiler",
            "default" => "{}",
          ),
          'profilers:badge' => array(
            'map' => 'profiler.nb_measures',
            'default' => 0,
          ),
        );
    }

    /**
     * Start timer
     *
     * @param string $timerId
     * @param array|null $tags
     * @return void
     */
    public function start($timerId, array $tags = null) {
        $this->_stat->start($timerId, microtime(true), memory_get_usage(true), memory_get_usage());
        $this->levels[$timerId] = $this->tabcount++;
    }

    /**
     * Stop timer
     *
     * @param string $timerId
     * @return void
     */
    public function stop($timerId){
        $this->_stat->stop($timerId, microtime(true), memory_get_usage(true), memory_get_usage());
        $this->tabcount --;
        $this->levels[$timerId] = $this->tabcount;
    }

    /**
     * Clear collected statistics for specified timer or for whole profiler if timer name is omitted.
     *
     * @param string|null $timerId
     * @return void
     */
    public function clear($timerId = null){
        $this->_stat->clear($timerId);
    }
}
