<?php
namespace JournalTransporterPlugin\Api;

class Response
{
    /**
     * @var mixed
     */
    protected $payload;

    /**
     * @var string
     */
    protected $contentType = 'text/json';

    /**
     * @var string
     */
    protected $responseCode = '200';

    /**
     * @param $payload
     * @param ((int|string)[]|string)[]|string $payload
     *
     * @psalm-param array{exception: string, route_regexes: list<array-key>}|string $payload
     */
    public function setPayload($payload): void
    {
        $this->payload = $payload;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param $contentType
     *
     * @psalm-param 'text/plain' $contentType
     */
    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param $responseCode
     * @param int|string $responseCode
     *
     * @psalm-param '404'|'500'|500 $responseCode
     */
    public function setResponseCode($responseCode): void
    {
        $this->responseCode = $responseCode;
    }

    /**
     * @return string
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }
}