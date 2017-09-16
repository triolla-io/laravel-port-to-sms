<?php

namespace Yna\PortToSms;

use DomainException;
use GuzzleHttp\Client as HttpClient;
use Yna\PortToSms\Exceptions\CouldNotSendNotification;

class PortToSmsApi
{
    const FORMAT_JSON = 3;

    /** @var string */
    protected $apiUrl = 'http://Port2SMS.com/Scripts/mgrqispi.dll';

    /** @var HttpClient */
    protected $httpClient;

    /** @var string */
    protected $accountId;

    /** @var string */
    protected $userID;

    /** @var string */
    protected $userPass;

    /** @var string */
    protected $sender;

    public function __construct($accountId, $userID, $userPass, $sender = null)
    {
        $this->accountId = $accountId;
        $this->userID = $userID;
        $this->userPass = $userPass;
        $this->sender = $sender;

        $this->httpClient = new HttpClient([
            'timeout' => 5,
            'connect_timeout' => 5,
        ]);
    }

    /**
     * @param  array $params
     *
     * @return array
     *
     * @throws CouldNotSendNotification
     */
    public function send($params)
    {
        $base = [
            'Appname' => 'Port2SMS',
            'prgname' => 'HTTP_SimpleSMS1',
            'AccountID' => $this->accountId,
            'UserID' => $this->userID,
            'UserPass' => $this->userPass,
            'Encoding' => 'utf-8'
        ];

        if (! empty($this->sender)) {
            $base['Sender'] = $this->sender;
        }

        $params = array_merge($params, $base);

        try {
            $response = $this->httpClient->get($this->apiUrl, ['query' => $params]);

            $response = json_decode((string) $response->getBody(), true);

            if (isset($response['error'])) {
                throw new DomainException($response['error'], $response['error_code']);
            }

            return $response;
        } catch (DomainException $exception) {
            throw CouldNotSendNotification::portToSmsRespondedWithAnError($exception);
        } catch (\Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithPortToSms($exception);
        }
    }
}
