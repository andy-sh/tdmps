<?php
/**
 * 时间处理类
 * 
 * @package module_g_tool
 * @subpackage model
 * @version $Id: class.time.inc.php 507 2013-05-20 08:02:20Z liqt $
 * @creator liqt @ 2013-01-15 11:17:25 by caster0.0.2
 */
namespace scap\module\g_tool;

/**
 * 时间处理类
 */
class time
{
    /**
     * 获取当前时间（含微秒）
     * 
     * @return array(0 => "Y-m-d H:i:s", 1 => 微秒数(6位整数))
     */
    public static function get_current_full_time()
    {
        $result = array();
        $times = explode(' ', microtime());
        $result[0] = date("Y-m-d H:i:s", $times[1]);
        $result[1] = $times[0]*1000000;// 微秒数（即百万分之一秒）
        
        return $result;
    }
    
    /**
     * 获取指定年份的ISO标准周数
     * - The o date format gives the ISO-8601 year number. We can use this,
     *  and the fact that "invalid" dates are automatically rolled around to make valid ones 
     *  (2011-02-31 gives 2011-03-03), to determine if a given year has 53 weeks. 
     *  If does not, then it must have 52.
     * 
     * @param int $year 年份，如 2013
     * 
     * @return int
     */
    public static function get_iso_weeks_in_year($year)
    {
        $date = new \DateTime;
        $date->setISODate($year, 53);
        return ($date->format("W") === "53" ? 53 : 52);
    }
    
    /**
     * 计算指定日期间隔的总星期数
     * - 目前仅支持跨度不超过两年
     * - 总星期数从1开始
     * - 按照自然周次计算，而不是按照天数被7除
     * 
     * @param string $start 开始日期:'Y-m-d'
     * @param string $end 结束日期:'Y-m-d'
     * 
     * @return int 总星期数量
     */
    public static function count_weeks($start, $end)
    {
        $result = 0;
        
        $start_time = strtotime($start);
        $end_time = strtotime($end);
        
        $start_year = date('o', $start_time);//ISO-8601 格式年份数字,每年的最后几天的星期数有可能对应的是下一年的第一周
        $start_weeks = date('W', $start_time);// 对应该年的星期数
        
        $end_year = date('o', $end_time);//ISO-8601 格式年份数字,每年的最后几天的星期数有可能对应的是下一年的第一周
        $end_weeks = date('W', $end_time);// 对应该年的星期数
        
        if ($start_year == $end_year)// 起止日期在同一年
        {
            $result = $end_weeks - $start_weeks + 1;
        }
        elseif ($start_year < $end_year)// 跨一年
        {
            
            $result = (self::get_iso_weeks_in_year($start_year) - $start_weeks + 1) + $end_weeks;
        }
        
        return $result;
    }
    
    /**
     * 根据周次及星期获取日期
     * - 目前仅支持跨度不超过两年
     * 
     * @example time::get_date_by_week(18, 3, '2012-9-10')// 获取从2012年9月10日开始，第18周周三的日期
     * 
     * @param int $weeks 相对于$weeks_from_date第几周
     * @param int $day_of_week 星期几，默认为1(周一)
     * @param string $weeks_from_date 参考的周次起始日期'Y-m-d'，默认NULL（当前年的第一天）
     * 
     * @return string 'Y-m-d'
     */
    public static function get_date_by_week($weeks, $day_of_week = 1, $weeks_from_date = NULL)
    {
        $result = NULL;
        
        if (empty($weeks_from_date))
        {
            $weeks_from_date = date('Y-01-01');
        }
        
        $weeks_from_time = strtotime($weeks_from_date);
        
        $iso_weeks_from_year = date('o', $weeks_from_time);// 起始日期的年份
        $iso_weeks_from_week = date('W', $weeks_from_time);// 起始日期的iso周数
        $iso_weeks_from_total_week = self::get_iso_weeks_in_year($iso_weeks_from_year);// 起始日期所在年份的总周数
        
        $iso_weeks = $iso_weeks_from_week + $weeks - 1;
        
        if ($iso_weeks > $iso_weeks_from_total_week)// 跨度超过一年
        {
            $iso_weeks_from_year += 1;// 起始年份加1
            $iso_weeks -= $iso_weeks_from_total_week;
        }
        
        $date = new \DateTime;
        $date->setISODate($iso_weeks_from_year, $iso_weeks, $day_of_week);
        
        $result = $date->format('Y-m-d');
        
        return $result;
    }
}
?>