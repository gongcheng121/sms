<?php
/**
 * Copyright (c) 2020.
 * author : koala<348222507@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace koalaGC\Sms;


use koalaGC\Sms\Contracts\GatewayInterface;
use koalaGC\Sms\Contracts\MessageInterface;

class Message implements MessageInterface
{
    protected $gateways = [];
    protected $content;
    protected $template;

    public function __construct(array $attributes=[])
    {
        foreach ($attributes as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getContent(GatewayInterface $gateway = null)
    {
        //content 属性 可传入function，传参为gateway
        return is_callable($this->content) ? call_user_func($this->content,$gateway):$this->content;
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(GatewayInterface $gateway = null)
    {
        return is_callable($this->template) ? call_user_func($this->template, $gateway) : $this->template;
    }

    /**
     * @inheritDoc
     */
    public function getGateWays()
    {
        // TODO: Implement getGateWays() method.
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param array $gateways
     */
    public function setGateways($gateways)
    {
        $this->gateways = $gateways;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }


    /**
     * @param $property
     *
     * @return string
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
}