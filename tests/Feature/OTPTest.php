<?php

namespace Ferdous\OtpValidator\Tests;

use Ferdous\OtpValidator\Services\OtpService;
use Orchestra\Testbench\TestCase;

class OTPTest extends TestCase
{
    /** #test */
    public function testGenerateOtpPrivateStaticMethod()
    {
        $otp = new OtpService();
        $otp_number = $this->invokeMethod($otp, 'otpGenerator', [10]);
        $this->assertTrue(is_numeric($otp_number));
    }

    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
