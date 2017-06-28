<?php
/**
 * @desc 配置文件
 * @author zhan <grianchan@gmail.com>
 * @since 2017/6/28 14:58
 */

return [
    'Endpoint' => '',
    'AccessKeyID' => '',
    'AccessKeySecret' => '',
    'TopicName' => 'sms.topic-cn-hangzhou',
    'SignName' => '容易装',
    'template' => [
        /* 工程信息有变更时，短信提示用户 【您的编号${number}的工程信息有变更。】 */
        'project_change' => 'SMS_73790023',
        /* 找回登陆密码  【您好，您找回密码验证码为${code}。】  */
        'template_find_password_key_name' => 'SMS_73730026',
        /* 注册验证码 【您好，您的验证码为${code}，请及时完成注册。】*/
        'template_register_key_name' => 'SMS_73860034',
        /* 首页浮动框发送报价到用户手机 【您的报价为：${price}】*/
        'send_quotes_price_to_mobile' => 'SMS_74400014',
        /* PC端预约量房验证码 【你好，预约量房验证码为${code}。】*/
        'house_measure' => 'SMS_74485016',
        /* PC端预约设计通知短信验证码 【你好，预约设计验证码为${code}。】*/
        'apply_new' => 'SMS_74495013',
        /* PC端快速报价通知报价短信 【您好，你的房屋报价为${price}，谢谢您的使用！】*/
        'rapid_quotation' => 'SMS_74305017',
        /* 样板间预约设计短信验证码 【你好，样板间预约设计验证码为${code}，请及时使用。】*/
        'ybj' => 'SMS_74345012',
        /* 工地直播短信验证码 【你好，验证码为${code}，请及时使用。】*/
        'site_live' => 'SMS_74300014',
        /* 首页浮动框获取快速报价模板验证码 【您好，您的获取快速报价验证码为${code}，请及时使用。】*/
        'application_apply' => 'SMS_74450021',
    ],
    'ttl' => [

    ]
];