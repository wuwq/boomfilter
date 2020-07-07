<?php
/**
 * User: wuqian
 * Date: 2020/7/7
 * Time: 11:42
 */

namespace boomfilter\Service;


class VicFilter
{
    /**
     * 查询某个字段是否需要过滤
     * @param $columnName
     * @return bool
     */
    static public function isFilterColumn($columnName){
        \Log::debug(__METHOD__.",字段名 => {$columnName}");
        //模糊字段数组
        $ambiguousColumns = ['name','desc','address','content','comment','login','contact','contacttitle'];

        foreach ($ambiguousColumns as $item){
            $strIndex = strpos($columnName,$item);
            if(!is_bool($strIndex)) return true;
            continue;
        }
        return false;
    }
}