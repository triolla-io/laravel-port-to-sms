<?php

namespace Yna\PortToSms;

use Illuminate\Notifications\Notification;
use Yna\PortToSms\Exceptions\CouldNotSendNotification;

class PortToSmsChannel
{
    /** @var PortToSmsApi */
    protected $portToSms;

    public function __construct(PortToSmsApi $portToSms)
    {
        $this->portToSms = $portToSms;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     *
     * @throws  \Yna\PortToSms\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $to = $notifiable->routeNotificationFor('port2sms');

        if (empty($to)) {
            throw CouldNotSendNotification::missingRecipient();
        }

        $message = $notification->toPortToSms($notifiable);

        if (is_string($message)) {
            $message = new PortToSmsMessage($message);
        }

        $this->sendMessage($to, $message);
    }

    protected function sendMessage($recipient, PortToSmsMessage $message)
    {
        if (mb_strlen($message->content) > 800) {
            throw CouldNotSendNotification::contentLengthLimitExceeded();
        }

        $params = [
            'Phone' => $recipient,
            'Text' => $message->content
        ];

        if (! empty($message->from)) {
            $params['Sender'] = $message->from;
        }

        $this->portToSms->send($params);
    }
}
