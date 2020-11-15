<?php
/**
 * @CreateTime:   2020/11/14 5:36 下午
 * @Author:       huizhang  <2788828128@qq.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  时间窗口计数器配置
 */
namespace TimeWindowCounter;

class Config
{

    public const UNIT_SECOND = 1;
    public const UNIT_MINUTE = 2;
    public const UNIT_HOUR = 3;
    public const UNIT_DAY = 4;

    protected $redis;

    public function getRedis() : \Redis
    {
        return $this->redis;
    }

    public function setRedis($redis)
    {
        $this->redis = $redis;
        return $this;
    }

}
