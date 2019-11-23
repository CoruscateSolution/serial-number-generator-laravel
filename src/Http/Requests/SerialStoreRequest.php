<?php

namespace CoruscateSolutions\SerialNumberGeneratorLaravel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class SerialStoreRequest extends FormRequest
{

    const UNPROCESSABLE_ENTITY = 422; // validation error

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the store request of Setting.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required',
            'financial_year' => 'required',
            'prefix' => 'required',
            'postfix' => 'nullable',
            'start_from' => 'required'
           
        ];
    }

    /**
     * Validation Fails and throw json response
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'data'=>null,
            'message'=>$validator->errors()->first(),
            'code'=>422
        ]));
    }
}
