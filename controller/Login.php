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

    public function __construct()
    {
        $this->timezone = new DateTimeZone("Asia/Shanghai");
        $this->timezoneUTC = new DateTimeZone("UTC");
        $this->initEvent();
    }

    public function initEvent()
    {
        $this->event = new stdClass();
        $startDate = "2021-03-01 00:00:00";
        $endDate = "2021-03-31 23:59:59";
        $this->event->startDate = new DateTime($startDate, $this->timezone);
        $this->event->endDate = new DateTime($endDate, $this->timezone);
        return true;
    }

    public function jsonResult($request)
    {
        $post = isset($request->post) ? $request->post : [];
        //@TODO 业务代码
        $now = new DateTime("now", $this->timezone);
        $diff = $this->event->startDate->diff($now); //8 days
        $nowStr = $now->format(DateTime::ISO8601);
        //$nowStr = date_format($now, 'Y-m-d\TH:i:sP'); //2021-03-09 09:07:22+08:00
        $eventStartStr = $this->event->startDate->format(DateTime::ISO8601);
        //$eventStartStr = date_format($this->event->startDate, 'Y-m-d\TH:i:sP'); //2013-04-12T16:40:00-04:00
        //change to UTC timezone
        //https://stackoverflow.com/questions/29019927/php-cant-set-correct-datetimezone-to-datetime
        //$nowUTC = $now->setTimeZone($this->timezoneUTC);
        $delay = 6;
        $testData = new DateTime("now", $this->timezone);
        $testData->modify("+{$delay} day");
        $source = array(
            "giftKey_1" => array(
                    "code" => "54402",
                    "msg" => "QualControl::QueryInfo acctKeyStr=354531|354531_giftKey_1|5901|0|666666 acct not existed",
                    "data" => false,
                ),
            "giftKey_2" => array(
                "code" => "54402",
                "msg" => "QualControl::QueryInfo acctKeyStr=354531|354531_giftKey_1|5901|0|666666 acct not existed",
                "data" => false,
            ),
        );
        if (array_key_exists("giftKey_1", $source)) {
            echo "yes";
        }
        else{
            echo "false";
        }
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