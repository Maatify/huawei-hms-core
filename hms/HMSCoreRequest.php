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

    protected array $params = [];

    protected function Curl()
    {
        if (! empty($this->url)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
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
            curl_setopt($ch, CURLOPT_FAILONERROR, false); // Required for HTTP error codes to be reported via our call to curl_error($ch)
            //        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Cache-Control: no-cache',
                'Content-Type: application/json',
                "Accept: application/json",
            ));
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_errno = curl_errno($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);
            if ($curl_errno > 0) {
                $response['success'] = false;
                $response['error'] = "(err-Fawry) cURL Error ($curl_errno): $curl_error";
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
                $this->url,
                $this->params,
                __METHOD__], 'Debug_' . __FUNCTION__);

            //            }

            return $response;
        }

        return ['success' => false];
    }

}