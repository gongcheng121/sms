<?php
/**
 * Copyright (c) 2020.
 * author : koala<348222507@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace koalaGC\Sms\Contracts;

/**
 * Interface MessageInterface
 * 消息体结构
 * @package koalaGC\Sms\Contracts
 */
interface MessageInterface
{
    /**
     * 获取消息内容
     * @param GatewayInterface|null $gateway
     * @return string
     */
    public function getContent(GatewayInterface $gateway = null);

    /**
     * 获取消息模板
     * @param GatewayInterface|null $gateway
     * @return string
     */
    public function getTemplate(GatewayInterface $gateway = null);

    /**
     * 返回支持网关
     * @return array
     */
    public function getGateWays();
}