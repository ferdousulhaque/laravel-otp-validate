# OTP Validate Package in Laravel
![Packagist Downloads](https://img.shields.io/packagist/dt/ferdous/laravel-otp-validate)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)
![Packagist Version](https://img.shields.io/packagist/v/ferdous/laravel-otp-validate)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/ferdous/laravel-otp-validate)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/ferdousulhaque/laravel-otp-validate)

This package is for easy setup for OTP validation process. No hassle, just plug and play. Following the steps mentioned below and you will be able to get a fully working OTP Validation system. You can use this later for authentication or e-commerce production selling, order confirmation.

[!["Buy Me A Coffee"](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png)](https://www.buymeacoffee.com/ferdousulhaque)

## Major change from version v2.0.1
Only Supports 7.4 and above & 8.0 and above, so any PHP versions below users will need to upgrade to use the latest version onwards.
* PHP version 7.4 and above
* PHP version 8.0 and above

If you are using PHP 7.4 below, please use version v1.4.0.

## Installation

### Install Package
Require this package with composer:
```
composer require ferdous/laravel-otp-validate
```
### Add Service Provider & Facade

#### For Laravel 5.5+
Once the package is added, the service provider and facade will be auto discovered.

#### For Older versions of Laravel
Add the ServiceProvider to the providers array in `config/app.php`:
```php
Ferdous\OtpValidator\OtpValidatorServiceProvider::class
```

Add the Facade to the aliases array in `config/app.php`:
```php
'OtpValidator' => Ferdous\OtpValidator\OtpValidatorServiceProvider::class
```

## Publish Config
Once done, publish the config to your config folder using:
```
php artisan vendor:publish --provider="Ferdous\OtpValidator\OtpValidatorServiceProvider"
```
This command will create a `config/otp.php` file.

### Email Configs
From the `.env` file the email configs are setup. No other changes required.

### SMS Configs
As the SMS Gateways use different methods and also extra headers and params, you may need to update the sms configs in the `otp.php` file.

## Migrate Database
Run the following command to create the otps table.
```
php artisan migrate
```
It will create a otps table with the required columns.

## Environment
Add the following Key-Value pair to the `.env` file in the Laravel application

```
# Basic OTP Configs
OTP_SERVICE='enabled'
OTP_TABLE_NAME='otps'
OTP_TIMEOUT=120
OTP_DIGIT=5
OTP_RESEND_SERVICE='enabled'
OTP_MAX_RETRY=2
OTP_MAX_RESEND=1
# Company and Service
OTP_SERVICE_NAME=
OTP_COMPANY_NAME=
# OTP via Email / SMS
OTP_SEND_BY_EMAIL=1
OTP_SEND_BY_SMS=1
# Email Configurations
OTP_EMAIL_FROM=
OTP_EMAIL_FROM_NAME=
OTP_EMAIL_SUBJECT=
# SMS Configurations
OTP_SMSC_URL='https://sms'
OTP_SMSC_METHOD=
OTP_COUNTRY_CODE=
OTP_SMSC_OVER_JSON=
OTP_SMSC_PARAM_TO_NAME=
OTP_SMSC_PARAM_MSG_NAME=
OTP_SMSC_USER=
OTP_SMSC_PASS=
AWS_SNS_VERSION=
AWS_SNS_KEY=
AWS_SNS_SECRET=
AWS_SNS_REGION=
```

## Definitions
Definition of the features in config are:

- service : enable/disable OTP Service
- timeout: timeout for OTP
- digit: OTP Digit
- resend-service: enable/disable resend Service
- max-retry: max retry for a single request
- max-resend: max resend for a single request
- service-name: for which the service is used
- company-name: for which company
- send-by: there are 3 ways to share otp (Email/SMS/AWS SNS)
- email: this key specifies the required information for email (e.g. from, name, subject etc.)
- sms: configure with SMS gateway to send SMS. 
(Universal Configurator)

## Defining Send By on Runtime

The config method can be used to set send-by [ SMS / Email / SNS ] at runtime.

    config('otp.send-by.email', 1);
    config('otp.send-by.sms', 0);

## OTP Request Templates
Once the template files are published, open `resources/views/vendor/template-otp/`

## Sample Controller
Run the following command to create a controller.

`php artisan make:controller OtpController`

Below is a sample for calling the OTP Validator in OtpController.

```php
namespace App\Http\Controllers;

use Ferdous\OtpValidator\Object\OtpRequestObject;
use Ferdous\OtpValidator\OtpValidator;
use Ferdous\OtpValidator\Object\OtpValidateRequestObject;

class OtpController extends Controller
{
    /**
     * @return array
     */
    public function requestForOtp()
    {
        return OtpValidator::requestOtp(
            new OtpRequestObject('1432', 'buy-shirt', '01711084714', 'ferdousul.haque@gmail.com')
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    public function validateOtp(Request $request)
    {
        $uniqId = $request->input('uniqueId');
        $otp = $request->input('otp');
        return OtpValidator::validateOtp(
            new OtpValidateRequestObject($uniqId,$otp)
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    public function resendOtp(Request $request)
    {
        $uniqueId = $request->input('uniqueId');
        return OtpValidator::resendOtp($uniqueId);
    }

}
```

Add the following to the `routes/web.php` file.

```
Route::get('/test/otp-request', 'OtpController@requestForOtp');
Route::get('/test/otp-validate', 'OtpController@validateOtp');
Route::get('/test/otp-resend', 'OtpController@resendOtp');
```

## Response/Error Descriptions
The below table describes the error codes generated in the response and their corresponding meanings.

```json
{
  "code": 201,
  "message": "OTP Sent to the recipient",
  "requestId": 1432,
  "type": "buy-shirt"
}
```

#### Request OTP Response Codes

| Code   |                Meanings
|--------|------------------------------------------
| 201    |  Successfully Generated OTP and shared.
| 400    |  Bad request.
| 501    |  Resend Service Disabled.
| 503    |  Service Unavailable.

#### OTP Validate Response Codes

| Code   |                Meanings
|--------|------------------------------------------
| 200    |  Correct OTP.
| 400    |  Invalid OTP.
| 404    |  OTP Expired/Not Found.
| 413    |  Max Retry Exceeded.

## License
MIT

## Special Thanks
- [Nahid Bin Azhar](https://github.com/nahid) For the Feedback.

## Support
- For any bugs, please help to create an issue.
- For any problem installing or configurations, feel free to knock me.
[ferdousul.haque@gmail.com](mailto:ferdousul.haque@gmail.com)

## Featured Article
- [How to create a laravel OTP/Security code verification for e-commerce website](https://medium.com/@ferdousul.haque/how-to-create-a-laravel-otp-security-code-verification-for-e-commerce-website-55de8161cfb8)

## Example SMS Gateways Configuration

### [Muthofun](https://www.muthofun.com/)
If you are trying to integrate one of most popular SMS gateway of Bangladesh, muthofun is a popular Bulk SMS Gateway in our country. Here is a sample configuration for the Muthofun SMS Gateway

```php
'smsc' => [
    'url' => env('OTP_SMSC_URL'),
    'method' => env('OTP_SMSC_METHOD', 'GET'),
    'add_code' => env('OTP_COUNTRY_CODE',null),
    'json' => env('OTP_SMSC_OVER_JSON',1),
    'headers' => [],
    'params' => [
        'send_to_param_name' => env('OTP_SMSC_PARAM_TO_NAME','number'),
        'msg_param_name' => env('OTP_SMSC_PARAM_MSG_NAME','msg'),
        'others' => [
            'user' => env('OTP_SMSC_USER'),
            'password' => env('OTP_SMSC_PASS'),
            'unicode' => 1
        ],
    ]
];
```

.env file will be as the following

```
OTP_SMSC_URL='http://clients.muthofun.com:8901/esmsgw/sendsms.jsp?'
OTP_SMSC_METHOD='GET'
OTP_COUNTRY_CODE='88'
OTP_SMSC_OVER_JSON=0
OTP_SMSC_PARAM_TO_NAME='mobiles'
OTP_SMSC_PARAM_MSG_NAME='sms'
OTP_SMSC_USER='YourUserName'
OTP_SMSC_PASS='YourPassWord'
```

### [Infobip](https://www.infobip.com/)
Example for integrating with the infobip SMS platform, renowned SMS Gateway.

using GET method

```php
'smsc' => [
    'url' => env('OTP_SMSC_URL'),
    'method' => env('OTP_SMSC_METHOD', 'GET'),
    'add_code' => env('OTP_COUNTRY_CODE',null),
    'json' => env('OTP_SMSC_OVER_JSON',1),
    'headers' => [],
    'params' => [
        'send_to_param_name' => env('OTP_SMSC_PARAM_TO_NAME','number'),
        'msg_param_name' => env('OTP_SMSC_PARAM_MSG_NAME','msg'),
        'others' => [
            'username' => env('OTP_SMSC_USER'),
            'password' => env('OTP_SMSC_PASS'),
            'from' => 'InfoSMS',
            'flash' => true
        ],
    ]
];
```

.env file will be as the following

```
OTP_SMSC_URL='https://{baseUrl}/sms/1/text/query?'
OTP_SMSC_METHOD='GET'
OTP_COUNTRY_CODE='88'
OTP_SMSC_OVER_JSON=0
OTP_SMSC_PARAM_TO_NAME='to'
OTP_SMSC_PARAM_MSG_NAME='text'
OTP_SMSC_USER='YourUserName'
OTP_SMSC_PASS='YourPassWord'
```

### [msg91](https://msg91.com)
Sample for integrating with the msg91 SMS gateway.

using GET method

```php
'smsc' => [
        'url' => env('OTP_SMSC_URL'),
        'method' => env('OTP_SMSC_METHOD', 'GET'),
        'add_code' => env('OTP_COUNTRY_CODE',null),
        'json' => env('OTP_SMSC_OVER_JSON',1),
        'headers' => [],
        'params' => [
            'send_to_param_name' => env('OTP_SMSC_PARAM_TO_NAME','number'),
            'msg_param_name' => env('OTP_SMSC_PARAM_MSG_NAME','msg'),
            'others' => [
                'authkey' => 'YourAuthKey',
                'sender' => 'YourSenderId',
                'route' => '1',
                'country' => '88',
            ],
        ],
        'wrapper' => 'sms',
    ];
```

.env file will be as the following

```
OTP_SMSC_URL='https://control.msg91.com/api/v2/sendsms?'
OTP_SMSC_METHOD='POST'
OTP_COUNTRY_CODE='88'
OTP_SMSC_OVER_JSON=1
OTP_SMSC_PARAM_TO_NAME='to'
OTP_SMSC_PARAM_MSG_NAME='text'
OTP_SMSC_USER='YourUserName'
OTP_SMSC_PASS='YourPassWord'
```

### [Using AWS Simple Notification Service (SNS)](https://aws.amazon.com/sns/)
Sample steps for integrating with the AWS SNS.

Create a IAM user with the appropriate policy permissions. Go to the IAM service and create your application’s user; be sure to capture its AWS Key and AWS Secret values and put this into your environment file. From there add the following policy to the user or its group.

```
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "AllowSendingSMSMessages",
            "Effect": "Allow",
            "Action": [
                "sns:Publish",
                "sns:CheckIfPhoneNumberIsOptedOut"
            ],
            "Resource": [
                "*"
            ]
        }
    ]
}
```

Here we set the ability to publish, check for opt-outs, and apply this across a wildcard resource instead of a specific topic as we will be sending notifications directly to phone numbers and not an SNS topic.
