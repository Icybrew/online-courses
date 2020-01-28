<?php


namespace App\Core\Routing\Exceptions;


/**
 * Interface IHttpException
 * @package App\Core\Routing\Exceptions
 */
interface IHttpException
{
    /**
     * @return mixed
     */
    public function getStatusCode();
}