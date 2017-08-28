<?php

namespace App\Http\Requests;

use App\Simsdm;
use App\UserAuth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreUserRequest extends FormRequest {
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Username harus diisi!',
        ];
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

        if (! empty($this->input('unit')))
        {
            $user_auths = UserAuth::where('username', Auth::user()->username)->get();

            // Is Super User, no need to check
            $found = $user_auths->filter(function ($v, $k)
            {
                if ($v->auth_type == 'SU')
                    return true;
            });
            if (! $found->isEmpty())
                return $ret;

            $allowed_sp = [];
            foreach ($user_auths as $user_auth)
            {
                if ($user_auth->auth_type == 'AU')
                {
                    $simsdm = new Simsdm();
                    $study_programs = $simsdm->studyProgram($user_auth->unit);
                    foreach ($study_programs as $study_program)
                    {
                        $allowed_sp[] = $study_program['name'];
                    }
                }
            }
            foreach ($this->input('auth_type') as $key => $item)
            {
                if ($item == 'AU')
                {
                    $found = $user_auths->filter(function ($v, $k) use ($key)
                    {
                        if ($v->auth_type == 'AU' && $v->unit == $this->input('unit')[$key])
                            return true;
                    });
                    if ($found->isEmpty())
                    {
                        $ret[] = 'Anda tidak diperbolehkan untuk membuat user pada unit ' . $this->input('unit')[$key] . ' !';
                    }
                } else
                {
                    $k = array_search($this->input('unit')[$key], $allowed_sp);
                    if (! $k)
                    {
                        $ret[] = 'Anda tidak diperbolehkan untuk membuat user pada unit ' . $this->input('unit')[$key] . ' !';
                    }
                }
            }
        }

        return $ret;
    }
}