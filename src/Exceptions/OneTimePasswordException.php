<?php

namespace Idez\NovaSecurity\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class OneTimePasswordException extends Exception implements HttpExceptionInterface
{
    /**
     * The status code.
     *
     * @var int
     */
    protected $statusCode = 401;

    /**
     * The headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @param int    $code
     * @param array  $headers
     * @param string $key
     */
    public function __construct($message = null, $code = 0, array $headers = [], $key = null)
    {
        parent::__construct($message, $code);

        $this->headers = $headers;

        if ($key) {
            $this->headers['X-OTP-KEY'] = $key;
        }
    }

    /**
     * Get the headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the headers.
     *
     * @param array $headers
     * @return OneTimePasswordException
     */
    public function setHeaders($headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Set the status code.
     *
     * @param int $statusCode
     *
     * @return $this
     */
    public function setStatusCode($statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Get the status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
