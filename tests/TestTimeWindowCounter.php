<?php
/**
 * @CreateTime:   2020/11/15 4:49 下午
 * @Author:       huizhang  <2788828128@qq.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  时间窗口计时器-单测
 */
namespace TimeWindowCounter\Tests;

use Redis;
use PHPUnit\Framework\TestCase;
use TimeWindowCounter\Config;
use TimeWindowCounter\TimeWindowCounter;

class TestTimeWindowCounter extends TestCase
{

    public function testIncrUnitSecond()
    {
        $obj = $this->getTimeWindowCounter();
        for ($i = 0; $i < 10; $i++) {
            $obj->incr('test', 1, Config::UNIT_SECOND);
        }
        $total = $obj->total('test', time() - 3, time());
        $this->assertEquals(10, $total);
    }

    public function testIncUnitMinute()
    {
        $obj = $this->getTimeWindowCounter();
        for ($i = 0; $i < 10; $i++) {
            $obj->incr('test', 1, Config::UNIT_MINUTE);
        }
        $total = $obj->total('test', time() - 60, time());
        $this->assertEquals(10, $total);
    }

    public function testIncUnitHour()
    {
        $obj = $this->getTimeWindowCounter();
        for ($i = 0; $i < 10; $i++) {
            $obj->incr('test', 1, Config::UNIT_HOUR);
        }
        $total = $obj->total('test', time() - 60*60, time());
        $this->assertEquals(10, $total);
    }

    public function testIncUnitDay()
    {
        $obj = $this->getTimeWindowCounter();
        for ($i = 0; $i < 10; $i++) {
            $obj->incr('test', 1, Config::UNIT_DAY);
        }
        $total = $obj->total('test', time() - 60*60*24, time());
        $this->assertEquals(10, $total);
    }

    public function testRestrore()
    {
        $obj = $this->getTimeWindowCounter();
        for ($i = 0; $i < 10; $i++) {
            $obj->incr('test', 1, Config::UNIT_SECOND);
        }
        $obj->restrore('test', 0, 100000000000000);
        $total = $obj->total('test', time() - 10, time());
        $this->assertEquals(0, $total);
    }

    public function getTimeWindowCounter() : TimeWindowCounter
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1',6379);
        $config = new Config();
        $config->setRedis($redis);
        $obj = new TimeWindowCounter();
        $obj->setConfig($config);
        $obj->restrore('test', 0, 100000000000000);
        return $obj;
    }

}
