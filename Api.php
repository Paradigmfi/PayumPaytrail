<?php
namespace Paradigm\PayumPaytrail;

use GuzzleHttp\Psr7\Request;
use Payum\Core\Bridge\Guzzle\HttpClientFactory;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\HttpClientInterface;

class Api
{
    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param array               $options
     * @param HttpClientInterface $client
     *
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     */
    public function __construct(array $options, HttpClientInterface $client = null)
    {
        $options = ArrayObject::ensureArrayObject($options);
        $options->validateNotEmpty(['merchantId', 'merchantSecret']);
        $this->options = $options;

        $this->client = $client ?: HttpClientFactory::create();
    }

    /**
     * @param array $fields
     *
     * @return array
     */
    public function createPayment(array $fields)
    {
        return $this->doRequest('POST', 'https://payment.paytrail.com/api-payment/create', $fields);
    }

    /**
     * @param array $fields
     *
     * @return array
     */
    protected function doRequest($method, $url, array $fields)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-Verkkomaksut-Api-Version' => 1,
            'Authorization' => 'Basic '.base64_encode($this->options['merchantId'].':'.$this->options['merchantSecret']),
        ];

        $request = new Request($method, $url, $headers, json_encode($fields));

        $response = $this->client->send($request);

        if (false == (
            ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) ||
            $response->getStatusCode() == 400
        )) {
            throw HttpException::factory($request, $response);
        }

        if (null === $body = json_decode($response->getBody(), true)) {
            throw HttpException::factory($request, $response);
        }


        return $body;
    }
}
