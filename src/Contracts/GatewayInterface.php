<?php
/**
 * Copyright (c) 2020.
 * author : koala<348222507@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Koala\Sms\Contracts;


use Koala\Sms\Config;

/**
 * Interface GatewayInterface
 * @package Koala\Sms\Contracts
 */
interface GatewayInterface
{

    /**
     * 获取网关名称
     * @return string
     */
    public function getName();

    /**
     * 发送短信
     * @param $to
     * @param MessageInterface $message
     * @param Config $config
     * @return array
     */
    public function send($to, MessageInterface $message, Config $config);
}