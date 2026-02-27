<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\HttpResponses;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseApiRequest extends FormRequest
{
    use HttpResponses;

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->error(
                'Validation error',
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $validator->errors()
            )
        );
    }
}
