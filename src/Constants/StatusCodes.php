<?php


namespace Ferdous\OtpValidator\Constants;


final class StatusCodes
{
    const SERVICE_UNAVAILABLE = 503;
    const BAD_REQUEST = 400;
    const RESEND_SERVICE_DISABLED = 503;
    const SUCCESSFULLY_SENT_OTP = 201;
    const OTP_VERIFIED = 200;
    const TOO_MANY_WRONG_RETRY = 413;
    const RESEND_EXCEEDED = 413;
    const INVALID_OTP_GIVEN = 400;
    const OTP_TIMEOUT = 404;
}
