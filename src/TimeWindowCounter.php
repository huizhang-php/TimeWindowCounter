<?php
/**
 * @CreateTime:   2020/11/14 5:33 下午
 * @Author:       huizhang  <2788828128@qq.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  时间窗口计数器
 */
namespace TimeWindowCounter;
use TimeWindowCounter\Core\LuaScript;

class TimeWindowCounter
{

    /** @var $config Config */
    protected $config;

    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }

    public function decr(string $key, int $decrValue=1, $unit=Config::UNIT_SECOND) : bool
    {
        return $this->incr($key, -$decrValue, $unit);
    }

    public function incr(string $key, int $incrValue=1, $unit=Config::UNIT_SECOND) : bool
    {
        $script = LuaScript::incr();
        $scriptId = sha1($script);
        $exists = $this->config->getRedis()->script('exists', $scriptId);
        if (empty($exists[0]))
        {
            $this->config->getRedis()->script('load', $script);
        }
        switch ($unit)
        {
            case Config::UNIT_MINUTE:
                $score = strtotime(date('Y-m-d H:i:00'));
                break;
            case Config::UNIT_HOUR:
                $score = strtotime(date('Y-m-d H:00:00'));
                break;
            case Config::UNIT_DAY:
                $score = strtotime(date('Y-m-d 00:00:00'));
                break;
            default:
                $score = time();
        }
        $res = $this->config->getRedis()->evalSha(
            $scriptId, [$key, $score, $incrValue], 3
        );
        return $res !== false;
    }

    public function total(string $key, int $startTime, int $endTime) : int
    {
        $script = LuaScript::total();
        $scriptId = sha1($script);
        $exists = $this->config->getRedis()->script('exists', $scriptId);
        if (empty($exists[0]))
        {
            $this->config->getRedis()->script('load', $script);
        }
        return $this->config->getRedis()->evalSha(
            $scriptId
            , [$key, $startTime, $endTime]
            ,3
        );
    }

    public function restrore(string $key, int $startTime, int $endTime)
    {
        return $this->config->getRedis()->zRemRangeByScore($key, $startTime, $endTime);
    }

}
