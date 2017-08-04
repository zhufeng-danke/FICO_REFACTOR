<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

//    'stripe' => [
//        'model' => App\User::class,
//        'key' => env('STRIPE_KEY'),
//        'secret' => env('STRIPE_SECRET'),
//    ],

    'qiniu' => [
        'key' => [
            //七牛坑爹的API规则导致了没法设置测试key，先放这里，然后定期换吧。
            'access' => env('QINIU_AKEY', 'AKwQsW7Gjjmrxh2rx6gfAH4PUBzAhmQDQ7ST0vUo'),
            'secret' => env('QINIU_SKEY', '32GC96pAQvYVVTyzeInfyKNyBJbyKpzs3bIafmR0'),
        ],
        'bucket' => [
            'private' => [
                'name' => 'private-files',
                'domain' => 'private.wutongwan.org',
            ],
            'public' => [
                'name' => 'public-files',
                'domain' => 'public.wutongwan.org',
            ],
        ]
    ],

];
