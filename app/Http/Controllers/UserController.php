<?php

namespace App\Http\Controllers;

use App\Simsdm;
use App\User;
use App\UserAuth;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use View;
use DB;
use parinpan\fanjwt\libs\JWTAuth;

class UserController extends MainController {
    public function __construct()
    {
        $this->middleware('is_auth');
        $this->middleware('is_operator');
        parent::__construct();

        $this->simsdm = new Simsdm();

        array_push($this->css['pages'], 'global/plugins/bower_components/fontawesome/css/font-awesome.min.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/animate.css/animate.min.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/jquery-ui/themes/base/jquery-ui.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/datatables/dataTables.bootstrap.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/datatables/datatables.responsive.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/select2/select2.min.css');

        array_push($this->js['plugins'], 'global/plugins/bower_components/datatables/jquery.dataTables.min.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/select2/select2.full.min.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/jquery-ui.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/ui/minified/autocomplete.min.js');

        array_push($this->js['scripts'], 'global/plugins/bower_components/datatables/dataTables.bootstrap.min.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/datatables/datatables.responsive.js');

        array_push($this->js['scripts'], 'js/customize.js');

        View::share('css', $this->css);
        View::share('js', $this->js);
    }

    public function index()
    {
        if (env('APP_ENV') == 'local')
        {
            $login = new \stdClass();
            $login->logged_in = true;
            $login->payload = new \stdClass();
            $login->payload->identity = env('USERNAME_LOGIN');
            $login->payload->user_id = env('ID_LOGIN');
//            $login->payload->identity = env('LOGIN_USERNAME');
//            $login->payload->user_id = env('LOGIN_ID');

        } else
        {
            $login = JWTAuth::communicate('https://akun.usu.ac.id/auth/listen', @$_COOKIE['ssotok'], function ($credential)
            {
                $loggedIn = $credential->logged_in;
                if ($loggedIn)
                {
                    return $credential;
                } else
                {
                    setcookie('ssotok', null, -1, '/');

                    return false;
                }
            }
            );
        }

        if (!$login)
        {
            $login_link = JWTAuth::makeLink([
                'baseUrl'  => 'https://akun.usu.ac.id/auth/login',
                'callback' => url('/') . '/callback.php',
                'redir'    => url('/'),
            ]);

            return view('landing-page', compact('login_link'));
        } else
        {
            $user = new User();
            $user->username = $login->payload->identity;
            $user->user_id = $login->payload->user_id;
            $user->full_name = $login->payload->name;
            $user->status = $login->payload->logged_in_as;
            $user->photo = $login->payload->user_unit->photo;
            $user->work_unit = $login->payload->user_unit->code;
            Auth::login($user);
            
            $this->setUserInfo();
            $page_title = 'Daftar Survey';

            return view('user.user-list', compact('page_title'));
        }
    }

    public function create()
    {
        $page_title = 'Tambah User';
        $auths = \App\Auth::all();
        $upd_mode = 'create';
        $action_url = 'users/create';

        $simsdm = new Simsdm();
        $units = $simsdm->unitAll();

        $user_auth = UserAuth::where('username',$this->user_info['username'])->get();

        $auth = null;

        if($user_auth->contains('auth_type','SSU')){
            $auth = 'SU';
        }elseif($user_auth->contains('auth_type','OPU')){
            $auth = 'OPU';
        }elseif($user_auth->contains('auth_type','OPF')){
            $auth = 'OPF';
        }

        return view('user.user-detail', compact(
            'page_title',
            'auths',
            'upd_mode',
            'action_url',
            'units',
            'auth'
        ));
    }

    public function store(StoreUserRequest $request)
    {
        $user_auths = new Collection();
        foreach ($request->input('auth_type') as $key => $item)
        {
            $user_auth = new UserAuth();
            $user_auth->username = $request->username;
            $user_auth->auth_type = $request->input('auth_type')[$key];
            $user_auth->unit = $request->input('unit')[$key];
            $user_auth->created_by = Auth::user()->username;
            $user_auths->push($user_auth);
        }

        $user_auths = $user_auths->unique(function ($item) {
            return $item['auth_type'] . $item['unit'];
        });
        DB::transaction(function () use ($user_auths) {
            foreach ($user_auths as $user_auth)
            {
                $user_auth->save();
            }
        });
        $request->session()->flash('alert-success', 'User berhasil ditambah');

        return redirect()->intended('users');
    }

    public function searchUser()
    {
        $input = Input::get();
        $simsdm = new Simsdm();
        $users = $simsdm->searchEmployee($input['query'], $input['limit']);

        $results = new Collection();
        foreach ($users->data as $user)
        {
            $result = new \stdClass();
            $result->username = $user->nip;
            $result->full_name = $user->full_name;
            $result->label = 'NIP: ' . $user->nip . ', NIDN: ' . $user->nidn . ', Nama: ' . $user->full_name;
            $results->push($result);
        }
        $results = json_encode($results, JSON_PRETTY_PRINT);

        return response($results, 200)->header('Content-Type', 'application/json');
    }

    public function getAjax()
    {
        $user_auths = UserAuth::all();
        $users = $user_auths->unique('username');
        $simsdm = new Simsdm();

        $data = [];

        $i = 0;
        foreach ($users as $user)
        {
            $data['data'][$i][0] = $i + 1;
            $data['data'][$i][1] = $user->username;
            $data['data'][$i][2] = $simsdm->searchEmployee($user->username,1)->data[0]->full_name;
            $data['data'][$i][3] = $user->auths()->first()->description;
            $i++;
        }

        $count_data = count($data);
        if ($count_data == 0)
        {
            $data['data'] = [];
        } else
        {
            $count_data = count($data['data']);
        }
        $data['iTotalRecords'] = $data['iTotalDisplayRecords'] = $count_data;
        $data = json_encode($data, JSON_PRETTY_PRINT);

        return response($data, 200)->header('Content-Type', 'application/json');
    }

    public function destroy()
    {
        $input = Input::get('username');
        $user_auths = UserAuth::where('username', $input)->get();
        if ($user_auths->isEmpty())
        {
            return abort('404');
        }
        UserAuth::where('username', $input)->delete();
        session()->flash('alert-success', 'User berhasil dihapus');

        return redirect()->intended('users');
    }

    public function edit()
    {
        $input = Input::get('id');
        $user_auths = UserAuth::where('username', $input)->get();
        if ($user_auths->isEmpty())
        {
            return abort('404');
        }

        $page_title = 'Edit User';
        $auths = \App\Auth::all();
        $upd_mode = 'edit';
        $action_url = 'users/edit';

        $simsdm = new Simsdm();

        $units = $simsdm->unitAll();

        $user_auth = UserAuth::where('username', $input)->first();
        $employee = $simsdm->searchEmployee($user_auth->username,1)->data[0];
        $user_auth->full_name = $employee->full_name;
        // $user_auth->username_display = "NIP: ".$employee->nip.", NIDN: ".$employee->nidn.", Nama: ".$employee->full_name;
        $user_auth->username_display = $input;
        $user_authent = UserAuth::where('username',$this->user_info['username'])->get();

        $auth = null;

        if($user_authent->contains('auth_type','SSU')){
            $auth = 'SU';
        }elseif($user_authent->contains('auth_type','OPU')){
            $auth = 'OPU';
        }elseif($user_authent->contains('auth_type','OPF')){
            $auth = 'OPF';
        }

        return view('user.user-detail', compact(
            'page_title',
            'auths',
            'upd_mode',
            'action_url',
            'units',
            'user_auths',
            'user_auth',
            'auth'
        ));
    }

    public function update(StoreUserRequest $request)
    {
        UserAuth::where('username', $request->username)->delete();
        if (is_null($request->input('auth_type')))
        {
            $request->session()->flash('alert-success', 'User berhasil dihapus');

            return redirect()->intended('users');
        } else
        {
            $user_auths = new Collection();
            foreach ($request->input('auth_type') as $key => $item)
            {
                $user_auth = new UserAuth();
                $user_auth->username = $request->username;
                $user_auth->auth_type = $request->input('auth_type')[$key];
                $user_auth->unit = $request->input('unit')[$key];
                $user_auth->created_by = Auth::user()->username;
                $user_auths->push($user_auth);
            }
            DB::transaction(function () use ($user_auths) {
                foreach ($user_auths as $user_auth)
                {
                    $user_auth->save();
                }
            });
            $request->session()->flash('alert-success', 'User berhasil diubah!');

            return redirect()->intended('users');
        }
    }
}