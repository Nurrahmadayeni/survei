<?php

namespace App\Http\Controllers;

use parinpan\fanjwt\libs\JWTAuth;

class CallbackController extends Controller {
    public function callback()
    {
        $auth = JWTAuth::recv([
            'ssotok'  => @$_GET['ssotok'],
            'secured' => true
        ]);

        if($auth)
            return 'Success';
        else
            return 'Fails';
    }
}
