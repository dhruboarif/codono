<?php

/**
 * HTTP Client library
 *
 * @author    Matt Bernier <dx@sendgrid.com>
 * @author    Elmer Thomas <dx@sendgrid.com>
 * @copyright 2016 SendGrid
 * @license   https://opensource.org/licenses/MIT The MIT License
 * @version   GIT: <git_id>
 * @link      http://packagist.org/packages/sendgrid/php-http-client
 */

namespace SendGrid;

/**
 * Holds the response from an API call.
 */
class Response
{
    /** @var int */
    protected $statusCode;
    /** @var string */
    protected $body;
    /** @var array */
    protected $headers;

    /**
     * Setup the response data
     *
     * @param int $statusCode the status code.
     * @param string $body the response body.
     * @param array $headers an array of response headers.
     */
    public function __construct($statusCode = null, $body = null, $headers = null)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }

    /**
     * The status code
     *
     * @return int
     */
    public function statusCode()
    {
        return $this->statusCode;
    }

    /**
     * The response body
     *
     * @return string
     */
    public function body()
    {
        return $this->body;
    }

    /**
     * The response headers
     *
     * @param bool $assoc
     *
     * @return array
     */
    public function headers($assoc = false)
    {
        if (!$assoc) {
            return $this->headers;
        }

        return $this->prettifyHeaders($this->headers);
    }

    /**
     * Returns response headers as associative array
     *
     * @param array $headers
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    private function prettifyHeaders($headers)
    {
        if (!is_array($headers)) {
            throw new \InvalidArgumentException('Headers should be an array');
        }

        return array_reduce(
            array_filter($headers),
            function ($result, $header) {
                if (empty($header)) {
                    return $result;
                }

                if (false === strpos($header, ':')) {
                    $result['Status'] = trim($header);

                    return $result;
                }

                list($key, $value) = explode(':', $header, 2);
                $result[trim($key)] = trim($value);

                return $result;
            },
            []
        );
    }
}
