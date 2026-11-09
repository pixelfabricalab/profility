<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'formatter' => [
            'class' => \yii\i18n\Formatter::class,
            'nullDisplay' => '',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error'],
                ],
            ],
        ],
        'opts' => [
            'class' => \common\helpers\OptionsListRetriever::class,
        ],
        'qr' => [
            'class' => \common\components\QRCode::class,
        ],
        'ocr' => [
            'class' => \common\components\Asprise::class,
        ],
        'scanner' => [
            'class' => \common\components\ScontrinoHelper::class,
        ],
        'punti' => [
            'class' => 'common\components\GestorePunti',
            'on accredito' => function($event) {
                \Yii::$app->mailer->compose()
                    ->setFrom('from@domain.com')
                    ->setTo('to@domain.com')
                    ->setSubject('Message subject')
                    ->setTextBody('Plain text content')
                    ->setHtmlBody($event->attivita)
                    ->send();
            }
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'authManager' => [
            'class' => \yii\rbac\DbManager::class,
        ],
        'mailer' => [
            'class' => \yii\swiftmailer\Mailer::class,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'sandbox.smtp.mailtrap.io',
                'username' => '14a56d9e181ab6',
                'password' => '1994c4c82241e0',
                'port' => '2525',
                'encryption' => 'tls',
            ],
        ],
    ],
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module',
            // your other grid module settings
        ],
        'gridviewKrajee' =>  [
            'class' => '\kartik\grid\Module',
            // your other grid module settings
        ],
    ],
];
