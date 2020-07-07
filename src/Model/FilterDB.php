<?php
/**
 * User: wuqian
 * Date: 2020/7/7
 * Time: 11:44
 */

namespace boomfilter\Model;


class FilterDB
{

    //敏感词前缀
    public $senFilterDBPrefix = "FL:sen:";

    private $config = [
        'scheme' => 'tcp',
        'host'   => 'redis',
        'port'   => 6379,
    ];

    /**
     * 设置过滤字段
     */
    public function setSenFilter($filterText,$filterLevel = 0){
        \Log::debug("---------敏感信息库-写入-------");
        $redisClient = new Predis\Client($this->config);
        $redisClient->select(9);
        $key = $this->senFilterDBPrefix.md5($filterText);
        \Log::debug("{$key} => {$filterLevel}");
        $redisClient->set($key,$filterLevel);
        $redisClient->select(0);
        return;
    }

    /**
     * 获取过滤字段
     */
    public function getSenFilter($filterText){
        $key = $this->senFilterDBPrefix.md5($filterText);
        \Log::debug("---------敏感信息库-命中 key => {$key}-------");
        $redisClient = new Predis\Client($this->config);
        $redisClient->select(9);
        $filterResult = $redisClient->get($key);
        $redisClient->select(0);
        return $filterResult;
    }

    /**
     * 获取过滤信息库
     */
    public function getFilterDB($filterText){

    }
}