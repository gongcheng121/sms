<?php
/**
 * Copyright (c) 2020.
 * author : koala<348222507@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace koalaGC\Sms\Strategies;


use koalaGC\Sms\Contracts\StrategyInterface;

class OrderStrategy implements StrategyInterface
{

    public function apply(array $gateways)
    {
        return array_keys($gateways);
    }
}