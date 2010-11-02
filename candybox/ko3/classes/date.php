<?php defined('SYSPATH') or die('No direct script access.');

class Date extends Kohana_Date
{
    public static function span_str($time1, $time2 = NULL, $output = 'years,months,weeks,days,hours,minutes,seconds')
    {
        $r = parent::span($time1, $time2, $output);
        foreach($r as $k => $v){
            if($v > 0){
                return $v.__($k);
            }
        }
    }

    public static function calender($y=null, $m=null)
    {
        $y = (int) $y;
        $m = (int) $m;

        $y = ($y >= 1) ? $y : date('Y');
        $m = ($m <= 12 AND $m >= 1) ? $m : date('n');

        // 下月
        $nm = $m + 1;
        if($nm > 12){
            $nm = 1;
            $ny = $y + 1;
        } else {
            $ny = $y;
        }

        // 上月
        $pm = $m - 1;
        if($pm < 1){
            $pm = 12;
            $py = $y - 1;
        } else {
            $py = $y;
        }

        $lastday = date("d", mktime(0, 0, 0, $nm, 0, $ny));
        $index = date("w", mktime(0, 0, 0, $m, 1, $y));

        // 返回本月最后一天跟第一天所在星期的位置
        return array(
            'lastday' => (int) $lastday,
            'start_at' => (int) $index,
        );
    }
}