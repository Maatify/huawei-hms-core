<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2024-03-10
 * Time: 10:19:18
 * https://www.Maatify.dev
 */

namespace Maatify\HMS;

use Maatify\Logger\Logger;

abstract class HMSCoreRequest
{
    private string $curl_method = 'POST';

    protected array $params = [
        'validate_only' => false,
        'message'       => [
            'notification' => [
                'title'        => 'Hello!',
                'body'         => 'Hello, shirley!',
                'click_action' => [
                    'type' => 3,
                ],
            ],

            'android' => [
                'urgency'      => 'NORMAL',
                'ttl'          => '10000s',
                'notification' => [
                    'title'        => 'Hello!',
                    'body'         => 'Hello, shirley!',
                    'click_action' => [
                        'type' => 3,
                    ],
                ],
            ],

            'token' =>
                [
                    "DEVICE_TOKEN",
                ],
        ],
    ];

    protected string $APP_ID_FROM_CONSOLE;
    private array $tokens;
    private string $title;
    private string $message;
    protected string|int $client_id;
    protected string $client_credentials;
    private string $access_token;

    public function __construct()
    {
        $this->CurlAuthorization();
        if (empty($this->access_token)) {
            Logger::RecordLog(['error' => 'no access token'], 'HMSErrors');
        }
    }

    public function SetTokens(array $tokens): static
    {
        $this->tokens = $tokens;

        return $this;
    }

    public function SetTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function SetMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function Load()
    {
        $this->params = [
            'validate_only' => false,
            'message'       => [
                'data' => json_encode([
                    'title_data' => $this->title,
                    'body_data'  => $this->message,
                ], true),

                'android' => [
                    'urgency'      => 'NORMAL',
                    'ttl'          => '10000s',
                    'notification' => [
                        'title'        => $this->title,
                        'body'         => $this->message,
                        'click_action' => [
                            'type' => 3,
                        ],
                    ],

                ],
                'token'   =>
                    $this->tokens,
            ],
        ];

        return $this->CurlMessage();
    }

    protected function CurlMessage()
    {
        $url = "https://push-api.cloud.huawei.com/v2/$this->APP_ID_FROM_CONSOLE/messages:send";
        if (! empty($this->access_token)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if (! empty($this->params)) {
                $this->curl_method = 'POST';
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->params));
            } else {
                if (empty($this->curl_method)) {
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                } else {
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->curl_method);
                    //                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                }
            }

            /*
            // no need returns from curl
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100);
            */
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1200);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);

            // Required for HTTP error codes to be reported via our call to curl_error($ch)
            curl_setopt($ch, CURLOPT_FAILONERROR, false);

            //            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Cache-Control: no-cache',
                'Content-Type: application/json',
                //                'Content-Type: application/x-www-form-urlencoded',
                "Accept: application/json",
                "Authorization: Bearer " . $this->access_token,
            ));
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_errno = curl_errno($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);
            if ($curl_errno > 0) {
                $response['success'] = false;
                $response['error'] = "(err-HMS) cURL Error ($curl_errno): $curl_error";
            } else {
                if ($resultArray = json_decode($result, true)) {
                    $response = $resultArray;
                    $response['success'] = true;
                } else {
                    $response['success'] = false;
                    $response['error'] = ($httpCode != 200) ? "Error header response " . $httpCode : "There is no response from server (err-" . __METHOD__ . ")";
                    $response['result'] = $result;
                }
            }

            //            if (empty($response['success']) || $response['status'] == 'error') {
            Logger::RecordLog([
                $response,
                $this->curl_method,
                $url,
                $this->params,
                __METHOD__], 'Debug_HMS_' . __FUNCTION__);

            //            }

            return $response;
        }

        return ['success' => false];
    }

    protected function CurlAuthorization()
    {
        $url = "https://oauth-login.cloud.huawei.com/oauth2/v3/token";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $params = [
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_credentials,
        ];
        $params = "grant_type=client_credentials&client_id=$this->client_id&client_secret=$this->client_credentials";
        $this->curl_method = 'POST';
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1200);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FAILONERROR, false); // Required for HTTP error codes to be reported via our call to curl_error($ch)
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Cache-Control: no-cache',
            'Content-Type: application/x-www-form-urlencoded',
            "Accept: application/json",
        ));
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);
        if ($curl_errno > 0) {
            $response['success'] = false;
            $response['error'] = "(err-HMS) cURL Error ($curl_errno): $curl_error";
        } else {
            if ($resultArray = json_decode($result, true)) {
                $response = $resultArray;
                $response['success'] = true;
                if (! empty($resultArray['access_token'])) {
                    $this->access_token = $resultArray['access_token'];
                }
            } else {
                $response['success'] = false;
                $response['error'] = ($httpCode != 200) ? "Error header response " . $httpCode : "There is no response from server (err-" . __METHOD__ . ")";
                $response['result'] = $result;
            }
        }
        Logger::RecordLog([
            $response,
            $this->curl_method,
            $url,
            $params,
            __METHOD__], 'Debug_HMS_' . __FUNCTION__);
        return $response;
    }

}