<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use phpseclib3\Math\PrimeField\Integer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class ProjectBaseRequest extends FormRequest
{
    protected const PATH_PROJECT_VALIDATION = '/project_validation';

    protected $stopOnFirstFailure = true;
    private string $validationCode = '08';

    abstract protected function getMessage(): string;
    abstract protected function getCode(): string;
    abstract protected function getAttributeValidation(): string;

    /**
     * @return void
     */
    public function prepareForValidation(): void
    {
        App::setLocale('code');
    }

    /**
     * @param string $attribute
     * @return string
     */
    private function getAttributeCode(string $attribute): string
    {
        return self::PATH_PROJECT_VALIDATION . '/' . $this->getAttributeValidation() . '.' . $attribute;
    }

    /**
     * @param Validator $validator
     * @return void
     */
    public function failedValidation(Validator $validator): void
    {
        $response = [];

        foreach ($validator->errors()->getMessages() as $attribute => $errors) {
            $transKey = $this->getAttributeCode($attribute);
            $res = trans($transKey);
            if ($transKey == $res) {
                throw new BadRequestHttpException($transKey);
            }
            $res = $this->getCode() . $this->validationCode . $res;
            $res .= array_shift($errors);
            $response['message'] = $this->getMessage();
            $response['error_code'] = (int) $res;
        }

        throw new HttpResponseException(response()->json(
            $response,
            Response::HTTP_BAD_REQUEST
        ));
    }
}
