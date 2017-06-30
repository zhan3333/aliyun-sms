<?php

/**
 * @desc
 * @author zhan <grianchan@gmail.com>
 * @since 2017/6/28 15:06
 */
class TestSms extends PHPUnit_Framework_TestCase
{
    /**
     * 随机生成一个手机号
     */
    public  function generateMobile()
    {
        $mobilePrefix = [
            130,131,132,133,134,135,136,137,138,139,
            144,147,
            150,151,152,153,155,156,157,158,159,
            176,177,178,
            180,181,182,183,184,185,186,187,188,189,
        ];
        return $mobilePrefix[array_rand($mobilePrefix)] . mt_rand(1000,9999) . mt_rand(1000,9999);
    }

    public function testSendSms()
    {
        /* 发送模板短信 */
        $config = require __DIR__ . '/aliyunsms.php';
        $sms = new MySms($config);
        $ret = $sms->send($this->generateMobile(), 'template_register_key_name', ['code' => '123456']);
        $this->assertTrue($ret);

        /* 自动储存与验证验证码 */
        $code = '543211';
        $mobile = $this->generateMobile();
        $ret = $sms->sendCode($mobile, 'house_measure', true, 'code', $code);
        $this->assertEquals($ret, $code);
        $checkRet = $sms->checkCode($mobile, 'house_measure', $code, true);
        $this->assertTrue($checkRet);

    }


}
