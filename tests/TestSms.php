<?php

/**
 * @desc
 * @author zhan <grianchan@gmail.com>
 * @since 2017/6/28 15:06
 */
class TestSms extends PHPUnit_Framework_TestCase
{
    public function testSendSms()
    {
        $config = require __DIR__ . '/aliyunsms.php';
        $sms = new MySms($config);
        $ret = $sms->send('13517210601', 'template_register_key_name', ['code' => '123456']);
        $this->assertTrue($ret);
    }
}
