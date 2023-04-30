<?php

namespace Light2\Services;

class ResponseService
{
    /**
     * HTTP Status Code
     * 
     * see: https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
     */
    protected array $responseCodeList = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        412 => 'Precondition Failed',
        423 => 'Locked',
        425 => 'Too Early',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        503 => 'Service Unavailable',
    ];

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     * @return int Status code.
     */
    public function getStatusCode(): int
    {
        return http_response_code();
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase(): string
    {
        return $this->responseCodeList[$this->getStatusCode()];
    }

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion(): string
    {
        return $_SERVER['SERVER_PROTOCOL'];
    }

    /**
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     * // Represent the headers as a string
     * foreach ($message->getHeaders() as $name => $values) {
     * echo $name . ": " . implode(", ", $values);
     * }
     *
     * // Emit headers iteratively:
     * foreach ($message->getHeaders() as $name => $values) {
     * foreach ($values as $value) {
     * header(sprintf('%s: %s', $name, $value), false);
     * }
     * }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     * @return array<array> Returns an associative array of the message's headers. Each
     *                      key MUST be a header name, and each value MUST be an array of strings
     *                      for that header.
     */
    public function getHeaders(): array
    {
        $headers = [];
        foreach (headers_list() as $values) {
            $values = explode(': ', $values);
            $headers[$values[0]] = $values[1];
        }

        return $headers;
    }

    /**
     * Checks if a header exists by the given name.
     *
     * @param string $name header field name.
     * @return bool Returns true if any header names match the given header
     *              name using a string comparison. Returns false if
     *              no matching header name is found in the message.
     */
    public function hasHeader(string $name): bool
    {
        return isset($this->getHeaders()[$name]) ? true : false;
    }

    /**
     * Retrieves a message header value by the given name.
     *
     * This method returns an array of all the header values of the given
     * header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name header field name.
     * @return array<string> An array of string values as provided for the given
     *                       header. If the header does not appear in the message, this method MUST
     *                       return an empty array.
     */
    public function getHeader(string $name): array
    {
        $header = $this->getHeaders();
        return isset($header[$name]) ? [$name => $header[$name]] : [];
    }

    /**
     * Sets the content type this response
     * 
     * @param string $mime
     * @param string $charset
     */
    public function setContentType(string $mime, string $charset = 'UTF-8'): void
    {
        header("Content-Type: $mime; charset=$charset");
    }

    /**
     * Generic response method
     * 
     * @param array $data
     * @param int $statusCode
     */
    public function respond(array $data = [], int $statusCode = 200): void
    {
        http_response_code($statusCode);
        $this->setContentType('application/json');
        if ($data != []) {
            echo json_encode($data);
        }
    }

    /**
     * Generic failure response
     * 
     * @param array $data
     * @param int $statusCode
     */
    public function fail(array $data = [], int $statusCode = 400): void
    {
        http_response_code($statusCode);
        $this->setContentType('application/json');
        if ($data != []) {
            echo json_encode($data);
        }
    }

    /**
     * Item created response
     * 
     * @param array $data
     */
    public function respondCreated(array $data = []): void
    {
        http_response_code(201);
        $this->setContentType('application/json');
        if ($data != []) {
            echo json_encode($data);
        }
    }

    /**
     * Command executed by no response required
     * 
     * @param array $data
     */
    public function respondNoContent(array $data = []): void
    {
        http_response_code(204);
        $this->setContentType('application/json');
        if ($data != []) {
            echo json_encode($data);
        }
    }

    /**
     * Client isn't authorized
     * 
     * @param array $data
     */
    public function failUnauthorized(array $data = []): void
    {
        http_response_code(401);
        $this->setContentType('application/json');
        if ($data != []) {
            echo json_encode($data);
        }
    }

    /**
     * Forbidden action
     * 
     * @param array $data
     */
    public function failForbidden(array $data = []): void
    {
        http_response_code(403);
        $this->setContentType('application/json');
        if ($data != []) {
            echo json_encode($data);
        }
    }

    /**
     * Resource Not Found
     * 
     * @param array $data
     */
    public function failNotFound(array $data = []): void
    {
        http_response_code(404);
        $this->setContentType('application/json');
        if ($data != []) {
            echo json_encode($data);
        }
    }

    /**
     * Resource already exists
     * 
     * @param array $data
     */
    public function failResourceExists(array $data = []): void
    {
        http_response_code(409);
        $this->setContentType('application/json');
        if ($data != []) {
            echo json_encode($data);
        }
    }

    /**
     * Resource previously deleted
     * 
     * @param array $data
     */
    public function failResourceGone(array $data = []): void
    {
        http_response_code(410);
        $this->setContentType('application/json');
        if ($data != []) {
            echo json_encode($data);
        }
    }

    /**
     * Client made too many requests
     * 
     * @param array $data
     */
    public function failTooManyRequests(array $data = []): void
    {
        http_response_code(429);
        $this->setContentType('application/json');
        if ($data != []) {
            echo json_encode($data);
        }
    }

    /**
     * Sets the appropriate status code to use when there is a server error.
     * 
     * @param array $data
     * @param int $statusCode
     */
    public function failServerError(array $data = [], int $statusCode = 500): void
    {
        http_response_code($statusCode);
        $this->setContentType('application/json');
        if ($data != []) {
            echo json_encode($data);
        }
    }
}