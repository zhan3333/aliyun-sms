<?php

/**
 * @desc
 * @author zhan <grianchan@gmail.com>
 * @since 2017/6/28 14:59
 */
class MySms extends \AliyunSms\Sms
{
    public function getCacheKeyPrefix($mobile, $templateKeyName)
    {
        // 获取缓存键值
        return $templateKeyName . '_' . $mobile;
    }

    /**
     * 从缓存中读验证码
     * @param $mobile
     * @param $templateKeyName
     * @return bool|string
     */
    public function getCacheCode($mobile, $templateKeyName)
    {
        $key = $this->getCacheKeyPrefix($mobile, $templateKeyName);
        $path = __DIR__ . '/' . $key;
        $code = file_get_contents($path);
        return $code;
    }

    /**
     * 删除缓存验证码
     * @param $mobile
     * @param $templateKeyName
     */
    public function delCacheCode($mobile, $templateKeyName)
    {
        $key = $this->getCacheKeyPrefix($mobile, $templateKeyName);
        $path = __DIR__ . '/' . $key;
        file_put_contents($path, '');
    }

    /**
     * 验证验证码是否正确
     * @param $mobile
     * @param $templateKeyName
     * @param $code
     * @param bool $isDelete
     * @return bool
     */
    public function checkCode($mobile, $templateKeyName, $code, $isDelete = true)
    {
        // 验证code是否正确
        if (empty($mobile) || empty($code)) return false;
        $cache_code = $this->getCacheCode($mobile,$templateKeyName);
        if (empty($cache_code)) {
            return false;
        } else {
            if ($code == $cache_code) {
                if ($isDelete) $this->delCacheCode($mobile, $templateKeyName);
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 保存验证码
     * @param $mobile
     * @param $templateKeyName
     * @param $code
     */
    public function saveCacheCode($mobile, $templateKeyName, $code)
    {
        // 保存code到缓存中
        $key = $this->getCacheKeyPrefix($mobile, $templateKeyName);
        $path = __DIR__ . '/' . $key;
        file_put_contents($path, $code);
    }

    /**
     * 发送完验证码后执行方法
     * @param array $data 短信参数
     * @param string $mobile 手机号
     * @param object $result 发送结果
     * @param string $template_key_name 模板名称
     * @param string $template_id 模板id
     * @param string $message_id 消息id
     */
    public function sendDo($data, $mobile, $result, $template_key_name, $template_id, $message_id)
    {
        // TODO: Implement sendDo() method.
    }
}