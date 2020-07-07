<?php
/**
 * User: wuqian
 * Date: 2020/7/7
 * Time: 11:09
 */

namespace boomfilter\Middleware;

use boomfilter\Model\FilterDB;
use boomfilter\Service\VicFilter;
use Closure;
use Lizhichao\Word\VicWord;


class BoomFilter
{
    /**
     * Handle an incoming request.
     *
     * VicWord分词示例："测试人员" => [ ['测试',5,'',1], ['人员',12,'',1] ]
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        \Log::debug("^^^^^Boom Filter^^^^^".__METHOD__);

        #过滤post、put
        if($request->method() != 'POST' && $request->method() != 'PUT') return $next($request);

        #请求参数分词
        $requestParams = $request->all();
        $vicword = new VicWord('json');

        foreach ($requestParams as $key=>$param){
            if(false === VicFilter::isFilterColumn($key)) continue;
            if(!is_string($param)) continue;
            $paramWords = $vicword->getAutoWord($param);
            \Log::debug($paramWords);
            $this->wordFilter($paramWords);
        }

        return $next($request);
    }


    /**
     * 分词做敏感信息检测
     * @param $wordArray
     * @throws \Exception
     */
    private function wordFilter($wordArray){
        \Log::debug("-^-^-^-^-分词检测-^-^-^-");
        $this->filterDBUpdate();

        // 分词检测
        $filterdb = new FilterDB();
        foreach ($wordArray as $word){
            \Log::debug("word =>{$word[0]}");
            $wordExist = $filterdb->getSenFilter($word[0]);
            if(!$wordExist) continue;
            throw new \Exception("存在敏感信息或违法信息",41201);
        }
    }

    /**
     * 检测与更新敏感信息过滤库
     */
    private function filterDBUpdate(){
        \Log::debug("-^-^-^-^-检测更新敏感信息过滤库-^-^-^-");

        //检查信息过滤库是否存在
        $filterdb = new FilterDB();
        $isExist = $filterdb->getSenFilter("十九大");
        if($isExist) return;

        //敏感信息入库
        Excel::load("resources/违法信息库.xls", function ($reader)use($filterdb){
            $reader = $reader->getSheet(0);
            $res = $reader->toArray();
            foreach ($res as $item){
                \Log::debug($item);
                $filterdb->setSenFilter($item[0],$item[1]);
            }
        });
        return;

    }
}