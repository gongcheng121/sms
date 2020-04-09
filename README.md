<h1 align="center"> sms </h1>

<p align="center"> 发送短信 SDK.</p>


## Installing

```shell
$ composer require koala/sms -vvv
```

## Usage

```
$config=[
            'default'=>[
                'gateways' => [
                    'huyi'
                ],
            ],
            'log'=>storage_path('smslog'),
            'gateways'=>[
                'huyi'=>[
                    'account'=>'***',
                    'password'=>'***'
                ],
                'dianxin'=>[
                    'access_token'=>'***',
                    'token_secret'=>'***',
                ]
            ]
        ];

//不使用短信模板
    $res = $sms->send('$mobile','您的验证码是：123456 。 请不要把验证码泄露给其他人。',['huyi']);
//使用短信模板
    $res = $sms->send('$mobile',['content'=>'您的验证码是：123456 。 请不要把验证码泄露给其他人。','template'=>'123'],['huyi']);

```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/koala/sms/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/koala/sms/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT