<?php

namespace Ferdous\OtpValidator\Services;

use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;
use Exception;
use Illuminate\Support\Facades\Log;
use Aws\Credentials\Credentials;

class SNSTransportService implements TransportServiceInterface
{
    /**
     * @var
     */
    private $number;

    /**
     * @var
     */
    private $otp;

    /**
     * @var
     */
    private $client;

    private string $service;
    private string $company;

    /**
     * AwsSnsTransportService constructor.
     * @param $number
     * @param $otp
     */
    public function __construct($number, $otp)
    {
        if (!$this->client) {
            // Configuration for the SNS
            $this->client = new SnsClient([
                'version' => config('otp.aws.sns.version'),
                'credentials' => new Credentials(
                    config('otp.aws.sns.credentials.key'),
                    config('otp.aws.sns.credentials.secret')
                ),
                'region' => config('otp.aws.sns.region'),
            ]);

            // For SMS Sending Reliability
            $this->client->SetSMSAttributes([
                'attributes' => [
                    'DefaultSMSType' => 'Transactional',
                ],
            ]);
        }

        $this->number = $number;
        $this->otp = $otp;
        $this->service = config('otp.service-name');
        $this->company = config('otp.company-name');
        return $this;
    }

    /**
     * @param string $otp
     * @param string $service
     * @param string $company
     * @return array|string
     */
    private function replaceOtpInTheTemplate(string $otp, string $service, string $company)
    {
        try {
            return view('vendor.template-otp.sms')
                ->with(['otp' => $otp, 'company' => $company, 'service' => $service])
                ->render();
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }
    }

    /**
     *
     */
    public function send()
    {
        try {
            $this->sendMessage(
                $this->number,
                $this->replaceOtpInTheTemplate($this->otp, $this->service, $this->company));
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * @param $to
     * @param $message
     */
    private function sendMessage($to, $message){
        try {
            $config = config('otp.smsc');
            $number = isset($config['add_code']) ? $config['add_code'] . $to : $to;

            $result = $this->client->publish([
                'Message' => $message,
                'PhoneNumber' => $number,
            ]);

            Log::info("OTP Validator: Number: {$number} AWS SNS Gateway Response Code: ".json_encode($result));
        } catch (AwsException $e) {
            // output error message if fails
            if (!empty($e->getMessage())) {
                $response = $e->getMessage();
                Log::error("OTP Validator: Number:{$number} AWS SNS Gateway Response Code: {$response}");
            }
        }
        return $this;
    }
}
