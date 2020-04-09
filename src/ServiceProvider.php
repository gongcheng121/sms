<?php
/**
 * Copyright (c) 2020.
 * author : koala<348222507@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace koalaGC\Sms;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Sms::class, function(){
            return new Sms(config('sms.key'));
        });

        $this->app->alias(Sms::class, 'sms');
    }

    public function provides()
    {
        return [Sms::class, 'sms'];
    }
}