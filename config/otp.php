<?php

return [
    'service' => env('OTP_SERVICE','enabled'),
    'table-name' => env('OTP_TABLE_NAME','otps'),
    'timeout' => env('OTP_TIMEOUT', 120),
    'digit' => env('OTP_DIGIT', 4),
    'resend' => env('OTP_RESEND_SERVICE', 'enabled'),
    'max-retry' => env('OTP_MAX_RETRY',2),
    'max-resend' => env('OTP_MAX_RESEND',1),
    'service-name' => env('OTP_SERVICE_NAME','OTP Service'),
    'company-name' => env('OTP_COMPANY_NAME','Test Company'),
    'send-by' => [
        'email' => env('OTP_SEND_BY_EMAIL',0),
        'sms' => env('OTP_SEND_BY_SMS',1),
        'aws-sns' => env('OTP_SEND_BY_AWS_SNS',0),
    ],
    'email' => [
        'from' => env('OTP_EMAIL_FROM','example@mail.com'),
        // Class string specifying the `Mailable` class to use when sending e-mail OTPs.
        // The constructor of the class will be called with a single string argument (the OTP
        // value).
        // If this config is left unspecified a default mailable class will be used.
        'mailable-class' => Ferdous\OtpValidator\Services\OtpMailable::class,
        'name' => env('OTP_EMAIL_FROM_NAME','Example'),
        'subject' => env('OTP_EMAIL_SUBJECT','Security Code')
    ],
    'smsc' => [
        'url' => env('OTP_SMSC_URL'),
        'method' => env('OTP_SMSC_METHOD', 'GET'),
        'add_code' => env('OTP_COUNTRY_CODE',null),
        'json' => env('OTP_SMSC_OVER_JSON',1),
        'headers' => [
            'header1' => '',
            'header2' => '',
            'authKey' => ''
            // Add the required headers
        ],
        'params' => [
            'send_to_param_name' => env('OTP_SMSC_PARAM_TO_NAME','number'),
            'msg_param_name' => env('OTP_SMSC_PARAM_MSG_NAME','msg'),
            'others' => [
                'username' => env('OTP_SMSC_USER'),
                'password' => env('OTP_SMSC_PASS'),
                'param1' => '',
                'param2' => ''
                // Add other params to send over request body/query
            ],
        ]
    ],
    'aws' => [
        'sns' => [
            'version' => env('AWS_SNS_VERSION','2010-03-31'),
            'credentials' => [
                'key' => env('AWS_SNS_KEY',null),
                'secret' => env('AWS_SNS_SECRET',null),
            ],
            'region' => env('AWS_SNS_REGION','us-east-1'),
            'profile' => env('AWS_SNS_PROFILE',null)
        ]
    ]
];
