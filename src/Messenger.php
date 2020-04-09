<?php
/**
 * Copyright (c) 2020.
 * author : koala<348222507@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace koalaGC\Sms;


use koalaGC\Sms\Contracts\MessageInterface;
use koalaGC\Sms\Exception\NoGatewayAvailableException;

class Messenger
{
    const STATUS_SUCCESS = 'success';

    const STATUS_FAILURE = 'failure';

    protected $sms;

    public function __construct(Sms $sms)
    {
        $this->sms = $sms;
    }


    public function send($to,MessageInterface $message,array $gateways=[]){
        $results = [];
        $isSuccessful = false;
        foreach ($gateways as $gateway => $config) {
            try {
                $results[$gateway] = [
                    'gateway' => $gateway,
                    'status' => self::STATUS_SUCCESS,
                    'result' => $this->sms->gateway($gateway)->send($to, $message, $config),
                ];
                $isSuccessful = true;
            } catch (\Exception $e) {
                $results[$gateway] = [
                    'gateway' => $gateway,
                    'status' => self::STATUS_FAILURE,
                    'exception' => $e,
                ];
            } catch (\Throwable $e) {
                $results[$gateway] = [
                    'gateway' => $gateway,
                    'status' => self::STATUS_FAILURE,
                    'exception' => $e,
                ];
            }
            if (!$isSuccessful) {
                throw new NoGatewayAvailableException($results);
            }
            return $results;
        }
    }
}