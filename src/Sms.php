<?php
/**
 * Copyright (c) 2020.
 * author : koala<348222507@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace koalaGC\Sms;


use Closure;
use koalaGC\Sms\Contracts\GatewayInterface;
use koalaGC\Sms\Contracts\MessageInterface;
use koalaGC\Sms\Contracts\StrategyInterface;
use koalaGC\Sms\Exception\InvalidArgumentException;
use koalaGC\Sms\Gateways\Gateway;
use koalaGC\Sms\Strategies\OrderStrategy;
use RuntimeException;

class Sms
{
    protected $config;

    private $guzzleOptions = [];
    /**
     * @var string
     */
    private $defaultGateway;
    private $gateways;
    private $strategies;
    /**
     * @var Messenger
     */
    private $messenger;
    private $customCreators;

    public function __construct(array $config)
    {
        $this->config = new Config($config);//将config 数组转为可操作对象
        if (!empty($config['default'])) {
            $this->setDefaultGateway($config['default']);
        }
    }


    public function send(string $to, $message, array $gateways = [])
    {
        $message = $this->formatMessage($message);
        $gateways =empty($gateways) ? $message->getGateWays() : $gateways;
        if (empty($gateways)){
            $gateways = $this->config->get('default.gateways', []);
        }
        return $this->getMessenger()->send($to, $message, $this->formatGateways($gateways));

    }

    /**
     * @param array $gateways
     * @return array
     * @throws InvalidArgumentException
     */
    protected function formatGateways(array $gateways)
    {
        $formatted = [];

        foreach ($gateways as $gateway => $setting) {
            if (\is_int($gateway) && \is_string($setting)) {
                $gateway = $setting;
                $setting = [];
            }

            $formatted[$gateway] = $setting;
            $globalSettings = $this->config->get("gateways.{$gateway}", []);

            if (\is_string($gateway) && !empty($globalSettings) && \is_array($setting)) {
                $formatted[$gateway] = new Config(\array_merge($globalSettings, $setting));
            }
        }

        $result = [];

        foreach ($this->strategy()->apply($formatted) as $name) {
            $result[$name] = $formatted[$name];
        }
        return $result;
    }

    /**
     * 设置网关策略
     * @param $strategy
     */
    public function strategy($strategy = null){

        if (\is_null($strategy)) {
            $strategy = $this->config->get('default.strategy', OrderStrategy::class);
        }
        if (!\class_exists($strategy)) {
            $strategy = __NAMESPACE__.'\Strategies\\'.\ucfirst($strategy);
        }

        if (!\class_exists($strategy)) {
            throw new InvalidArgumentException("Unsupported strategy \"{$strategy}\"");
        }
        if (empty($this->strategies[$strategy]) || !($this->strategies[$strategy] instanceof StrategyInterface)) {
            $this->strategies[$strategy] = new $strategy($this);
        }
        return $this->strategies[$strategy];

    }


    protected function formatMessage($message)
    {
        if (!$message instanceof MessageInterface) {
            if (!is_array($message)) {
                $message = [
                    'content' => $message,
                    'template' => $message
                ];
            }
            $message = new  Message($message);
        }
        return $message;
    }
    /**
     * Set default gateway name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setDefaultGateway($name)
    {
        $this->defaultGateway = $name;

        return $this;
    }

    public function gateway($name=null){
        $name = $name ?: $this->getDefaultGateway();
        if (!isset($this->gateways[$name])) {
            $this->gateways[$name] = $this->createGateway($name);
        }

        return $this->gateways[$name];
    }

    private function getDefaultGateway()
    {
        if (empty($this->defaultGateway)) {
            throw new RuntimeException('No default gateway configured.');
        }

        return $this->defaultGateway;
    }

    private function getMessenger()
    {
        return $this->messenger ?: $this->messenger = new Messenger($this);
    }

    /**
     * 扩展网关
     * @param $name
     * @param Closure $callback
     * @return $this
     */
    public function extend($name, Closure $callback)
    {
        $this->customCreators[$name] = $callback;

        return $this;
    }

    private function createGateway($name)
    {
        if (isset($this->customCreators[$name])) {
            $gateway = $this->callCustomCreator($name);
        } else {
            $className = $this->formatGatewayClassName($name);
            $config = $this->config->get("gateways.{$name}", []);

            if (!isset($config['timeout'])) {
                $config['timeout'] = $this->config->get('timeout', Gateway::DEFAULT_TIMEOUT);
            }

            $gateway = $this->makeGateway($className, $config);
        }

        if (!($gateway instanceof GatewayInterface)) {
            throw new InvalidArgumentException(\sprintf('Gateway "%s" must implement interface %s.', $name, GatewayInterface::class));
        }

        return $gateway;
    }

    private function callCustomCreator($gateway)
    {
        return \call_user_func($this->customCreators[$gateway], $this->config->get("gateways.{$gateway}", []));

    }

    private function formatGatewayClassName($name)
    {
        if (\class_exists($name) && \in_array(GatewayInterface::class, \class_implements($name))) {
            return $name;
        }

        $name = \ucfirst(\str_replace(['-', '_', ''], '', $name));

        return __NAMESPACE__."\\Gateways\\{$name}Gateway";
    }

    private function makeGateway($className, $config)
    {
        if (!\class_exists($className) || !\in_array(GatewayInterface::class, \class_implements($className))) {
            throw new InvalidArgumentException(\sprintf('Class "%s" is a invalid sms gateway. 不存在的网关', $className));
        }

        return new $className($config);
    }
}