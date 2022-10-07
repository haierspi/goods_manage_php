<?php
namespace ff\helpers;

class Time
{
    private static $weekList = [];

    private static $execStartTime = [];
    private static $exeTotalTime = [];

    /**
     * 获取指定年份的每一周的起止时间
     *
     * @param [type] $year
     * @return void
     */
    public static function execTime($key = 'default', $end = null)
    {

        $time = explode(' ', microtime());
        if (is_null($end)) {
            $end = 0;
        } else {
            $end = 1;
        }
        if ($end) {
            $execTime = self::$exeTotalTime[$key] + round($time[0] + $time[1] - (self::$execStartTime[$key][0] + self::$execStartTime[$key][1]), 3);
            self::$exeTotalTime[$key] = $execTime;
            return $execTime;
        } else {
            self::$execStartTime[$key] = $time;
        }

    }

    /**
     * 根据条件校验并获取起止时间
     *
     * @param integer $year
     * @param integer $month
     * @param integer $week
     * @param integer $day
     * @return void
     */
    public static function checkAndGetDateTime($year = 0, $month = 0, $week = 0, $day = 0)
    {
        $startTime = '';
        $endTime = '';
        //校验年份
        if ((floor($year) != $year) || ($year < 1970)) {
            return false;
        }

        if ($month != 0 && $week == 0 && $day == 0) {
            //校验月份
            if ((floor($month) != $month) || ($month < 1) || ($month > 12)) {
                return false;
            } else {
                //按月获取
                list($startTime, $endTime) = self::getMounthDate($year, $month);
            }

        } elseif ($week != 0 && $day == 0 && $month == 0) {
            //获取周列表
            $weekList = self::getWeekList($year);
            //校验周是否存在
            if (!isset($weekList[$week])) {
                return false;
            } else {
                list($startTime, $endTime) = self::getWeekDate($year, $week);
            }

        } elseif ($day != 0 && $month == 0 && $week == 0) {
            // 校验日
            $date = $year . "-" . $month . "-" . $day;
            $unixTime = strtotime($date);
            if (!$unixTime) {
                return false;
            } else {

                $startTime = date("Y-m-d 00:00:00", strtotime($date));
                $endTime = date("Y-m-d 23:59:59", strtotime($date));
            }

        } else {
            // 按年获取
            $yearStart = $year . "-01-01";
            $yearEnd = $year . "-12-31";

            $startTime = date("Y-m-d 00:00:00", strtotime($yearStart));
            $endTime = date("Y-m-d 23:59:59", strtotime($yearEnd));
        }
        return [$startTime, $endTime];
    }

    /**
     * 获取指定年份的每一周的起止时间
     *
     * @param [type] $year
     * @return void
     */
    public static function getWeekList($year)
    {
        if (empty(self::$weekList)) {
            $weekList = [];
            $yearStart = $year . "-01-01";
            $yearEnd = $year . "-12-31";

            $startDate = strtotime($yearStart);
            if (intval(date('N', $startDate)) != '1') {
                $startDate = strtotime("next monday", strtotime($yearStart)); //获取年第一周的日期
            }
            $yearMondy = date("Y-m-d 00:00:00", $startDate); //获取年第一周的日期

            $endday = strtotime($yearEnd);
            if (intval(date('W', $endday)) == '7') {
                $endday = strtotime("last sunday", strtotime($yearEnd));
            }

            $num = intval(date('W', $endday));
            for ($i = 1; $i <= $num; $i++) {
                $j = $i - 1;
                $startDay = date("Y-m-d 00:00:00", strtotime("$yearMondy $j week "));

                $endDay = date("Y-m-d 23:59:59", strtotime("$startDay +6 day"));

                $weekList[$i] = [
                    $startDay,
                    $endDay,
                ];
            }
            self::$weekList = $weekList;

        }

        return self::$weekList;
    }

    /**
     * 获取指定年份的指定周的起止时间
     *
     * @param [type] $year
     * @param [type] $week
     * @return void
     */
    public static function getWeekDate($year, $week)
    {

        $weekList = self::getWeekList($year);

        return $weekList[$week];
    }

    /**
     * 获取指定年份的指定月的起止时间
     *
     * @param [type] $year
     * @param [type] $week
     * @return void
     */
    public static function getMounthDate($year, $month)
    {

        $day = date('t', strtotime($year . '-' . $month));
        $startTime = date("Y-m-d H:i:s", strtotime($year . '-' . $month));
        $endTime = date("Y-m-d H:i:s", mktime(23, 59, 59, $month, $day, $year));

        return [$startTime, $endTime];
    }

    /**
     * 获取当前日期所在自然周
     *
     * @param [type] $year
     * @param [type] $week
     * @return void
     */
    public static function getWeekNum($date)
    {

        $yearStartTime = strtotime(date("Y-1-1 00:00:00", strtotime($date)));
        $date = strtotime(date("Y-m-d 00:00:00", strtotime($date)));
        $weekNum = floor((($date - $yearStartTime) / (60 * 60 * 24 * 7)));

        return $weekNum;
    }

    /**
     * 求两个日期之间相差的天数
     * (针对1970年1月1日之后，求之前可以采用泰勒公式)
     * @param [string] $afterDate
     * @param [string] $beforeDate
     * @return number
     */
    public static function diffBetweenTwoDays($afterDate, $beforeDate)
    {
        $datetimeAfter = new \DateTime($afterDate);
        $datetimeBefore = new \DateTime($beforeDate);
        return $datetimeAfter->diff($datetimeBefore)->days;
    }

    /**
     * 求两个日期之间相差的天数,带小数点
     * (针对1970年1月1日之后，求之前可以采用泰勒公式)
     * @param string $afterDate
     * @param string $beforeDate
     * @return float
     */
    public static function diffDecimalsBetweenTwoDays($afterDate, $beforeDate)
    {
        $datetimeAfter = strtotime($afterDate);
        $datetimeBefore = strtotime($beforeDate);
        return (float) sprintf("%.2f", ($datetimeAfter - $datetimeBefore) / 86400);
    }

    /**
     * 求两个日期之间相差的天数 以小时为单位
     *
     * @param [string] $afterDate
     * @param [string] $beforeDate
     * @return float
     * @Author HaierSpi haierspi@qq.com
     * @DateTime 2020-04-23
     */
    public static function diffBetweenTwoDaysByHour($afterDate, $beforeDate)
    {

        $datetimeAfter = new \DateTime($afterDate);
        $datetimeBefore = new \DateTime($beforeDate);

        return sprintf("%.2f", ($datetimeAfter->getTimestamp() - $datetimeBefore->getTimestamp()) / 3600 / 24);
    }
}
