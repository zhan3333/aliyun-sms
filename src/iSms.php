<?php
namespace AliyunSms;

/**
 * @desc
 * @author zhan <grianchan@gmail.com>
 * @since 2017/5/27 8:37
 */
interface iSms
{

    const TEMPLATE_REGISTER_KEY_NAME = 'template_register_key_name';
    const TEMPLATE_FIND_PASSWORD_KEY_NAME = 'template_find_password_key_name';
    const DEFAULT_TTL = 5;

    public function __construct($smsConfig);

    /**
     * 发送验证码短信
     * @param string $mobile
     * @param $templateKeyName
     * @param string $codeName
     * @param null|string $code    自定义验证码
     * @return mixed
     */
    public function sendCode($mobile, $templateKeyName, $codeName = 'code', $code = null);

    /**
     * 发送短信
     * @param string    $mobile             手机号
     * @param string    $templateKeyName    模板名称
     * @param array     $data               模板参数
     * @return mixed
     */
    public function send($mobile, $templateKeyName, $data);

    /**
     * 获取短信的名称
     * @return string
     */
    public function getSmsName();

    public function setSendCallback($callback);

    public function checkCode($mobile, $templateKeyName, $code);

    public function saveCacheCode($mobile, $templateKeyName, $code);

    public function getCacheCode($mobile, $templateKeyName);

    public function delCacheCode($mobile, $templateKeyName);

    public function getCacheKeyPrefix($mobile, $templateKeyName);
}