<?php


namespace App\Core\Routing\Exceptions;


use Throwable;

class HttpException extends \Exception implements IHttpException
{
    private $_statusCode;

    public function __construct(int $statusCode, string $message = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->_statusCode;
    }
}
