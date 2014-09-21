<?php
require 'CurrentTime.php';

/**
 * Class Banner
 */
Class Banner
{
    private $_allowedIP = ['10.0.0.1', '10.0.0.2'];

    private $From;

    private $To;

    private $CurrentTime;

    /**
     * @param  string $from
     * @param  string $to
     */
    public function __construct($from, $to)
    {
        $this->From = new DateTime($from);
        $this->To = new DateTime($to);
        $this->CurrentTime = new CurrentTime();
    }

    /**
     * @param CurrentTime $CurrentTime
     */
    public function attach(CurrentTime $CurrentTime)
    {
        $this->CurrentTime = $CurrentTime;
    }

    /**
     * @return bool
     */
    public function isDisplayable()
    {
        $Now = $this->CurrentTime->getCurrentTime();

        //IP判定
        if (in_array($_SERVER['REMOTE_ADDR'], $this->_allowedIP)) {
            return true;
        }

        //表示時間内か否か(Fromは以上・Toは以下で判定)
        return (int)$Now->diff($this->From)->format('%r%s') <= 0 && (int)$Now->diff($this->To)->format('%r%s') >= 0;
    }


}