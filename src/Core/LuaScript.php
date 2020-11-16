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
        local key=KEYS[1];
        local score=KEYS[2];
        local incrValue=KEYS[3];
        local value = redis.call('ZRANGEBYSCORE', key, score, score);
        local nextValue = incrValue;
        if (type(value) == 'table' and #value > 0)
        then
            local lastValue = string.gsub(value[1], score .. '_', '',1)
            nextValue = lastValue+incrValue;
            redis.call('ZREMRANGEBYSCORE', key, score, score);
            redis.call('ZADD', key, score, score .. '_' .. nextValue);
        else
            redis.call('ZADD', key, score, score .. '_' .. nextValue);
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
        local key = KEYS[1];
        local startScore = KEYS[2];
        local endScore = KEYS[3];
        local array = redis.call('ZRANGEBYSCORE', key, startScore, endScore);
        for i=1, #array do
            local start = string.find(array[i], '_', 1)+1; 
            if (start == false)
            then
            
            else
                result = result + string.sub(array[i], start);
            end
        end
        return result;
SCRIPT;
    }

}
