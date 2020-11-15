<?php
/**
 * @CreateTime:   2020/11/14 5:55 下午
 * @Author:       huizhang  <2788828128@qq.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  lua 脚本
 */
namespace TimeWindowCounter\Core;

class LuaScript
{

    /**
     * 保证每个score只有一个member, 并实现member的自增
     *
     * @return string
     * CreateTime: 2020/11/15 2:18 下午
     */
    public static function incr()
    {
        return <<<SCRIPT
        local nextValue=KEYS[3];
        local value = redis.call('ZRANGEBYSCORE', KEYS[1], KEYS[2], KEYS[2]);
        if (type(value) == 'table' and #value > 0)
        then
            nextValue = value[1]+KEYS[3];
            redis.call('ZREMRANGEBYSCORE', KEYS[1], KEYS[2], KEYS[2]);
            redis.call('ZADD', KEYS[1], KEYS[2], nextValue);
        else
            redis.call('ZADD', KEYS[1], KEYS[2], nextValue);
        end
        return nextValue;
SCRIPT;
    }

    /**
     * 统计
     *
     * @return string
     * CreateTime: 2020/11/15 2:19 下午
     */
    public static function total()
    {
        return <<<SCRIPT
        local result = 0;
        local array = redis.call('ZRANGEBYSCORE', KEYS[1], KEYS[2], KEYS[3]);
        for i=1, #array do
            result = result + array[i];
        end
        return result;
SCRIPT;
    }

}
