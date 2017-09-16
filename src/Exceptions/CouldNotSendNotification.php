<?php

namespace Yna\PortToSms\Exceptions;

use Exception;
use DomainException;

class CouldNotSendNotification extends Exception
{
    /**
     * Thrown when recipient's phone number is missing.
     *
     * @return static
     */
    public static function missingRecipient()
    {
        return new static('Notification was not sent. Phone number is missing.');
    }

    /**
     * Thrown when content length is greater than 800 characters.
     *
     * @return static
     */
    public static function contentLengthLimitExceeded()
    {
        return new static(
            'Notification was not sent. Content length may not be greater than 800 characters.'
        );
    }

    /**
     * Thrown when we're unable to communicate with Port2SMS.com.
     *
     * @param  DomainException $exception
     *
     * @return static
     */
    public static function portToSmsRespondedWithAnError(DomainException $exception)
    {
        return new static(
            "Port2SMS.com responded with an error '{$exception->getCode()}: {$exception->getMessage()}'"
        );
    }

    /**
     * Thrown when we're unable to communicate with Port2SMS.com.
     *
     * @param  Exception $exception
     *
     * @return static
     */
    public static function couldNotCommunicateWithPortToSms(Exception $exception)
    {
        return new static("The communication with Port2SMS.com failed. Reason: {$exception->getMessage()}");
    }
}
