<?php
require "Banner.php";

/**
 * Class BannerTest
 */
class BannerTest extends PHPUnit_Framework_TestCase
{

    Const REFERENCE_TIME = "2014-01-01 00:00:00";

    /**
     * setUp
     *
     * @return void
     */
    public function setUp()
    {
        //許可されているIPをデフォルト値として設定
        $_SERVER["REMOTE_ADDR"] = '10.0.0.1';

        //実行時間でテスト結果に差が出ないように現在時刻を一定にセット
        $this->CurrentTime = $this->getMock('CurrentTime');
        $this->CurrentTime->method('getCurrentTime')
            ->willReturn(new DateTime(self::REFERENCE_TIME));
    }

    /**
     * 設定時間に関わらず、特定IPならtrueが返るテスト
     *
     * @return void
     */
    public function testIsDisplayableSuccessSpecifiedIp()
    {
        $this->Banner = new Banner('2000-01-01 00:00:00', '2000-01-01 00:00:00');
        $this->Banner->attach($this->CurrentTime);
        $this->assertTrue($this->Banner->isDisplayable());
        $_SERVER["REMOTE_ADDR"] = '10.0.0.2';
        $this->Banner = new Banner('2000-01-01 00:00:00', '2000-01-01 00:00:00');
        $this->Banner->attach($this->CurrentTime);
        $this->assertTrue($this->Banner->isDisplayable());
    }

    /**
     * 正常系成功
     *
     * @return void
     */
    public function testIsDisplayableNormal()
    {
        //許可されていないIPを設定
        $_SERVER["REMOTE_ADDR"] = '192.168.0.1';

        //From,Toを一秒前後に設定
        $from = (new DateTime(self::REFERENCE_TIME))->modify('-1 second')->format('Y-m-d h:i:s');
        $to = (new DateTime(self::REFERENCE_TIME))->modify('+1 second')->format('Y-m-d h:i:s');
        $this->Banner = new Banner($from, $to);
        $this->Banner->attach($this->CurrentTime);
        $this->assertTrue($this->Banner->isDisplayable());

        //From,Toを同時刻に設定
        $from = $to = (new DateTime(self::REFERENCE_TIME))->format('Y-m-d h:i:s');
        $this->Banner = new Banner($from, $to);
        $this->Banner->attach($this->CurrentTime);
        $this->assertTrue($this->Banner->isDisplayable());
    }

    /**
     * From, Toの誤った設定による失敗
     *
     * @return void
     */
    public function testIsDisplayableFailureNormal()
    {
        //許可されていないIPを設定
        $_SERVER["REMOTE_ADDR"] = '192.168.0.1';

        //Now < From,To に設定
        $from = $to = (new DateTime(self::REFERENCE_TIME))->modify('+1 second')->format('Y-m-d h:i:s');
        $this->Banner = new Banner($from, $to);
        $this->Banner->attach($this->CurrentTime);
        $this->assertFalse($this->Banner->isDisplayable());

        //Now > From,To に設定
        $from = $to = (new DateTime(self::REFERENCE_TIME))->modify('-1 second')->format('Y-m-d h:i:s');
        $this->Banner = new Banner($from, $to);
        $this->Banner->attach($this->CurrentTime);
        $this->assertFalse($this->Banner->isDisplayable());

        //To < From < Now に設定
        $from = (new DateTime(self::REFERENCE_TIME))->modify('-1 second')->format('Y-m-d h:i:s');
        $to = (new DateTime(self::REFERENCE_TIME))->modify('-2 second')->format('Y-m-d h:i:s');
        $this->Banner = new Banner($from, $to);
        $this->Banner->attach($this->CurrentTime);
        $this->assertFalse($this->Banner->isDisplayable());

        //Now < From < Toに設定
        $from = (new DateTime(self::REFERENCE_TIME))->modify('+1 second')->format('Y-m-d h:i:s');
        $to = (new DateTime(self::REFERENCE_TIME))->modify('+2 second')->format('Y-m-d h:i:s');
        $this->Banner = new Banner($from, $to);
        $this->Banner->attach($this->CurrentTime);
        $this->assertFalse($this->Banner->isDisplayable());

        //To < Now < From に設定
        $from = (new DateTime(self::REFERENCE_TIME))->modify('+1 second')->format('Y-m-d h:i:s');
        $to = (new DateTime(self::REFERENCE_TIME))->modify('-1 second')->format('Y-m-d h:i:s');
        $this->Banner = new Banner($from, $to);
        $this->Banner->attach($this->CurrentTime);
        $this->assertFalse($this->Banner->isDisplayable());
    }

    /**
     * 一つ目の引数が正しく日時として解釈出来ない文字列の場合例外を投げるテスト
     *
     * @expectedException        Exception
     * @expectedExceptionMessage DateTime::__construct(): Failed to parse time string
     */
    public function testIsDisplayableDateTimeFailure1()
    {
        $this->Banner = new Banner('hoge', '2014-01-02 00:00:00');
    }

    /**
     * 二つ目の引数が正しく日時として解釈出来ない文字列の場合例外を投げるテスト
     *
     * @expectedException        Exception
     * @expectedExceptionMessage DateTime::__construct(): Failed to parse time string
     */
    public function testIsDisplayableDateTimeFailure2()
    {
        $this->Banner = new Banner('2014-01-01 00:00:00', 'hoge');
    }
}