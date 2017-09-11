<?php
/**
 * @desc
 * @author zhan <grianchan@gmail.com>
 * @since 2017/6/28 9:48
 */

namespace AliyunSms;

require_once __DIR__ . '/sdk/mns-autoloader.php';

use AliyunMNS\Client;
use AliyunMNS\Model\BatchSmsAttributes;
use AliyunMNS\Model\MessageAttributes;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Requests\PublishMessageRequest;


/**
 * @property  endPoint
 */
abstract class Sms implements iSms
{
    private $endPoint;
    private $accessId;
    private $accessKey;
    private $client;
    private $topic;
    private $signName;

    /**
     * @var mixed
     */
    public $smsConfig;

    /**
     * AliyunSms constructor.
     * @param $smsConfig
     */
    public function __construct($smsConfig)
    {
        /**
         * Step 1. 初始化Client
         */
        $this->endPoint = $smsConfig['Endpoint']; // eg. http://1234567890123456.mns.cn-shenzhen.aliyuncs.com
        $this->accessId = $smsConfig['AccessKeyID'];
        $this->accessKey = $smsConfig['AccessKeySecret'];
        $this->client = new Client($this->endPoint, $this->accessId, $this->accessKey);
        /**
         * Step 2. 获取主题引用
         */
        $topicName = $smsConfig['TopicName'];
        $this->topic = $this->client->getTopicRef($topicName);

        $this->signName = $smsConfig['SignName'];
        $this->smsConfig = $smsConfig;
    }

    /**
     * 发送验证码短信
     * @param string $mobile
     * @param $templateKeyName
     * @param bool $autoSave
     * @param string $codeName
     * @param null|string $code 自定义验证码
     * @return mixed
     */
    public function sendCode($mobile, $templateKeyName, $autoSave = false, $codeName = 'code', $code = null)
    {
        if (empty($code)) {
            $cache_code = $this->getCacheCode($mobile, $templateKeyName);
            if (! empty($cache_code)) {
                $code = $cache_code;
            } else {
                $code = $this->randNumber(6);
            }
        }
        $result = $this->send($mobile, $templateKeyName, [$codeName => $code]);
        if ($result) {
            // 发送成功
            if ($autoSave) {
                // 储存到缓存中
                $this->saveCacheCode($mobile, $templateKeyName, $code);
            }
            return $code;
        } else {
            return false;
        }
    }

    /**
     * 发送短信
     * @param string $mobile 手机号
     * @param string $templateKeyName 模板名称
     * @param array $data 模板参数
     * @return bool 是否发送成功
     * @throws SmsException|MnsException
     */
    public function send($mobile, $templateKeyName, $data)
    {
        $mobile = (string)$mobile;
        if (!empty($data) && is_array($data)) {
            foreach ($data as &$item) {
                if (is_scalar($item)) $item = (string) $item;
            }
        }
        /**
         * Step 3. 生成SMS消息属性
         */
        // 3.1 设置发送短信的签名（SMSSignName）和模板（SMSTemplateCode）
        $templateId = $this->smsConfig['template'][$templateKeyName];
        if (empty($templateId)) throw new SmsException('aliyunsms.template.' . $templateKeyName . ' 未找到配置');
        $batchSmsAttributes = new BatchSmsAttributes($this->signName, $templateId);
        // 3.2 （如果在短信模板中定义了参数）指定短信模板中对应参数的值
        $batchSmsAttributes->addReceiver($mobile, $data);
        $messageAttributes = new MessageAttributes(array($batchSmsAttributes));
        /**
         * Step 4. 设置SMS消息体（必须）
         *
         * 注：目前暂时不支持消息内容为空，需要指定消息内容，不为空即可。
         */
        $messageBody = "smsmessage";
        /**
         * Step 5. 发布SMS消息
         */
        $request = new PublishMessageRequest($messageBody, $messageAttributes);

        $result = $this->topic->publishMessage($request);
        // 发送完成后执行
        $this->sendDo($data, $mobile, $result, $templateKeyName, $templateId, $result->getMessageId());
        return $result->isSucceed();
    }

    /**
     * 获取短信的名称
     * @return string
     */
    public function getSmsName()
    {
        return 'Aliyun';
    }

    /**
     * @param $templateKeyName
     */
    public function getTtl($templateKeyName)
    {
        if (isset($this->smsConfig['ttl'][$templateKeyName])) {
            return $this->smsConfig['ttl'][$templateKeyName] * 60;
        } else {
            return $this::DEFAULT_TTL * 60;
        }
    }

    /**
     * 生成随机验证码
     * @param int $length
     * @return int
     */
    private function randNumber($length = 6)
    {
        if($length < 1)
        {
            $length = 6;
        }

        $min = 1;
        for($i = 0; $i < $length - 1; $i ++)
        {
            $min = $min * 10;
        }
        $max = $min * 10 - 1;

        return rand($min, $max);
    }

    /**
     * 获取客户端ip
     * @return array|false|string
     */
    public function getClientIp() {
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else
            $ip = "Unknow";
        return $ip;
    }

    // 需要子类实现的

    /**
     * 获取缓存的key name
     * @param $mobile
     * @param $templateKeyName
     * @return mixed
     */
    abstract function getCacheKeyPrefix($mobile, $templateKeyName);

    /**
     * 从缓存中读验证码
     * @param $mobile
     * @param $templateKeyName
     * @return bool|string
     */
    abstract function getCacheCode($mobile, $templateKeyName);

    /**
     * 删除缓存验证码
     * @param $mobile
     * @param $templateKeyName
     */
    abstract function delCacheCode($mobile, $templateKeyName);

    /**
     * 验证验证码是否正确
     * @param $mobile
     * @param $templateKeyName
     * @param $code
     * @param bool $isDelete
     * @return bool
     */
    abstract function checkCode($mobile, $templateKeyName, $code, $isDelete = true);

    /**
     * 保存验证码
     * @param $mobile
     * @param $templateKeyName
     * @param $code
     */
    abstract function saveCacheCode($mobile, $templateKeyName, $code);

    /**
     * 发送完验证码后执行方法
     * @param array $data           短信参数
     * @param string $mobile               手机号
     * @param object $result               发送结果
     * @param string $template_key_name    模板名称
     * @param string $template_id          模板id
     * @param string $message_id           消息id
     */
    abstract function sendDo($data, $mobile, $result, $template_key_name, $template_id, $message_id);
}