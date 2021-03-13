<?php


require "./model/EventRulesModel.php";

class Login
{

    /**
     * event info
     * @var dynamic object
     * https://krisjordan.com/blog/2008/11/27/dynamic-properties-in-php-with-stdclass
     */
    public $event;
    private $timezone;
    private $timezoneUTC;

    public function __construct() {
        $this->timezone = new DateTimeZone("Asia/Shanghai");
        $this->timezoneUTC = new DateTimeZone("UTC");
        $this->initEvent();
    }
    public function initEvent()
    {
        $this->event= new stdClass();
        $startDate = "2021-03-01 00:00:00";
        $endDate = "2021-03-31 23:59:59";
        $this->event->startDate = new DateTime($startDate, $this->timezone);
        $this->event->endDate = new DateTime($endDate, $this->timezone);
        return true;
    }

    public function index($request)
    {
        $post = isset($request->post) ? $request->post : [];

        //@TODO 业务代码
        $now = new DateTime("now", $this->timezone);
        $diff = $this->event->startDate->diff($now); //8 days
        $eventStartStr = date_format($this->event->startDate, 'Y-m-d H:i:sP'); //2021-03-01 00:00:00+08:00
        $nowStr = date_format($now, 'Y-m-d H:i:sP'); //2021-03-09 09:07:22+08:00

        //change to UTC timezone
        //https://stackoverflow.com/questions/29019927/php-cant-set-correct-datetimezone-to-datetime
        //$nowUTC = $now->setTimeZone($this->timezoneUTC);
        $testData = $this->date();
        $res = array(
            "now" => $now,
            "nowStr" => $nowStr,
            "eventStart" => $this->event->startDate,
            "evenStartStr" => $eventStartStr,
            "diff" => $diff,
            "testData" => $testData,
            "eventRules" => EventRules::Instance());
        return json_encode($res);
    }

    protected function date($format = 'Y-m-d H:i:s', $timestamp = null, $timezone = null)
    {
        if (empty($timestamp)) {
            $timestamp = time();
        }
        if (empty($timezone)) {
            $timezone = "Asia/Shanghai";
        }
        $dtz = new \DateTimeZone($timezone);
        $dt = new \DateTime(null, $dtz);
        $dt->setTimestamp($timestamp);
        return $dt->format($format);
    }
}