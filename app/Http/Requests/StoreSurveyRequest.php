<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'title'                     => 'required',
            'start_date'                => 'required',
            'end_date'                  => 'required',
            'unit'                      => 'required',
            'is_subject'                => 'required',
        ];

        if($this->input('mat_kul') == '0'){
            $rules = array_add($rules, 'sample', 'required');
        }
        return $rules;
    }

    protected function getValidatorInstance()
    {
        return parent::getValidatorInstance()->after(function ($validator)
        {
            $this->after($validator);
        });
    }

    public function after($validator)
    {
        $check = $this->checkBeforeSave();
        if (count($check) > 0)
        {
            foreach ($check as $item)
            {
                $validator->errors()->add('alert-danger', $item);
            }
        }
    }

    private function checkBeforeSave()
    {
        $ret = [];
        if($this->input('auth')!= 'SU' && $this->input('auth')!= 'OPU' && $this->input('auth')!= 'OPF'){
            $ret[] = 'Anda tidak mempunyai hak akses untuk menyimpan survey ini';
        }

        return $ret;
    }
}