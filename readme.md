# OTP Validate Package in Laravel
This package is for easy setup for OTP validation process. No hassle, just plug and play. Following the steps mentioned below and you will be able to get a fully working OTP Validation system. You can use this later for authentication or e-commerce production selling, order confirmation.

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
OTP_TIMEOUT=120
OTP_DIGIT=5
OTP_RESEND_SERVICE=
OTP_MAX_RETRY=2
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
```

## Definitions
Definition of the features in config are:

- service : enable/disable OTP Service
- timeout: timeout for OTP
- digit: OTP Digit
- enable/disable resend Service
- max-retry: max retry for a single request
- service-name: for which the service is used
- company-name: for which company
- send-by: there are 2 ways to share otp (Email/SMS)
- email: this key specifies the required information for email (e.g. from, name, subject etc.)

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
            new OtpRequestObject('1432', '01711084714', 'buy-shirt','ferdousul.haque@gmail.com')
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    public function validateOtp(Request $request)
    {
        $uniqId = $request->input('id');
        $otp = $request->input('otp');
        return OtpValidator::validateOtp(
            new OtpValidateRequestObject($uniqId,$otp)
        );
    }

    /**
     * @return array
     */
    public function resendOtp()
    {
        return OtpValidator::requestOtp(
            new OtpRequestObject('1432', '01711084714', 'buy-shirt','ferdousul.haque@gmail.com',1)
        );
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
  "code": 200,
  "status": true,
  "requestId": 1234,
  "type": "buy-shirt"
}
```

| Code   |                Meanings
|--------|------------------------------------------
| 200    |  Successfully Generated OTP and shared.
| 203    |  Exceeded retry count.
| 204    |  Invalid Otp.
| 403    |  Bad request.
| 404    |  OTP Service disabled.

## License
MIT

## Support
- For any bugs, please create an issue.
- For any problem installing or configurations, feel free to knock me.
[ferdousul.haque@gmail.com](mailto:ferdousul.haque@gmail.com)
