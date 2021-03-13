<?php

final class EventRules
{
    public $timeZone;
    /**
     * Call this method to get singleton
     *
     * @return EventRules
     */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new EventRules();
        }
        return $inst;
    }

    /**
     * Private ctor so nobody else can instantiate it
     *
     */
    private function __construct()
    {
        $this->timeZone = new \DateTimeZone("Asia/Shanghai");
    }
}
