<?php

namespace App\Http\Requests\Sorting;

use Illuminate\Foundation\Http\FormRequest;

class ShowSortingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }

}
