<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\ProjectBaseRequest;

abstract class PostBaseRequest extends ProjectBaseRequest
{
    abstract protected function getMessage(): string;

    /**
     * @return string
     */
    protected function getCode(): string
    {
        return '3';
    }

    /**
     * @return string
     */
    protected function getAttributeValidation(): string
    {
        return  'post_attribute_validation';
    }
}
