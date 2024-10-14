<?php

class ApiResponse
{
    public string|bool $response;
    public int $httpCode;
    public string $curlError;

    /**
     * @param CurlHandle $curl
     */
    public function __construct(CurlHandle $curl)
    {
        $this->response = curl_exec($curl);
        $this->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->curlError = curl_error($curl);
    }

    public function __toString(): string
    {
        $object = [
            "httpCode" => $this->httpCode,
            "response" => json_decode($this->response, true),
            "curlError" => json_decode($this->curlError, true),
        ];
        return ("<pre>" . json_encode($object, JSON_PRETTY_PRINT) . "</pre>");
    }
}