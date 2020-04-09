<?php
/**
 * Copyright (c) 2020.
 * author : koala<348222507@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Koala\Sms\Gateways;


use Koala\Sms\Config;
use Koala\Sms\Contracts\MessageInterface;
use Koala\Sms\Exception\GatewayErrorException;
use Koala\Sms\Traits\HasHttpRequest;

/**
 * 互易无线发送短信接口封装
 *
 * @package Koala\Sms\Gateways
 */
class HuyiGateway extends Gateway
{

    use HasHttpRequest;
    const API_URL = 'http://106.ihuyi.cn/webservice/sms.php?method=Submit';
    const FORMAT = 'json';
    const SUCCESS_CODE = 2; //成功码

    /**
     * @inheritDoc
     */
    public function send($to, MessageInterface $message, Config $config)
    {

        $params = [
            'account'=>$config->get('account'),
            'mobile'=>$to,
            'content'=>$message->getContent($this),
            'password'=>$config->get('password'),
            'format'=>self::FORMAT
        ];
        $result = $this->post(self::API_URL,$params);
        if (self::SUCCESS_CODE != $result['code']) {
            throw new GatewayErrorException($result['msg'], $result['code'], $result);
        }
        return $result;
    }
}