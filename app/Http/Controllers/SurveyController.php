<?php

namespace App\Http\Controllers;

use App\Question;
use App\Survey;
use App\SurveyObjective;
use App\UserAnswer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\StoreSurveyRequest;
use App\Simsdm;
use App\User;
use App\UserAuth;
use View;
use parinpan\fanjwt\libs\JWTAuth;

class SurveyController extends MainController
{
    protected $simsdm;

    public function __construct()
    {
        $this->middleware('is_auth')->except('index');
        $this->middleware('is_operator')->except('index', 'ajaxSurvey','answer','answerStore');


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
            Auth::login($user);

            $this->setUserInfo();
            $page_title = 'Daftar Survei';

            $user_auth = UserAuth::where('username',$this->user_info['username'])->get();

            $auths = null;

            if($user_auth->contains('auth_type','SU')){
                $auths = 'SU';
            }elseif($user_auth->contains('auth_type','OPU')){
                $auths = 'OPU';
            }elseif($user_auth->contains('auth_type','OPF')){
                $auths = 'OPF';
            }

            return view('survey.survey-list', compact('page_title', 'auths'));
        }
    }

    public function create()
    {
        array_push($this->css['pages'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/css/datepicker.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/bootstrap-daterangepicker/css/daterangepicker.css');

        array_push($this->js['scripts'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/js/bootstrap-datepicker.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/bootstrap-daterangepicker/js/daterangepicker.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/jquery-validation/dist/jquery.validate.min.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/jquery-ui.js');
        array_push($this->js['plugins'], 'js/loadingoverlay.min.js');

        View::share('css', $this->css);
        View::share('js', $this->js);

        $upd_mode = 'create';
        $action_url = 'survey/create';
        $page_title = 'Tambah Survei';
        $disabled = '';

        $survey = new Survey();
        $survey->sample = new Collection();

        $user_auth = UserAuth::where('username',$this->user_info['username'])->get();
        $auth = null;

        if($user_auth->contains('auth_type','SU')){
            $auth = 'SU';
        }elseif($user_auth->contains('auth_type','OPU')){
            $auth = 'OPU';
        }else{
            $auth = 'OPF';
        }

        $simsdm = new Simsdm();
        $units = [];

        if($user_auth->contains('auth_type','SU')){
            $units = $simsdm->unitAll();
            $usu = array("id"=>"","code"=>"USU","name"=>"Universitas Sumatera Utara");
            array_push($units,$usu);
        }else{
            foreach ($user_auth as $user){
                $list_units = $simsdm->unitAll();

                foreach ($list_units as $key=>$unit){
                    if (is_array($list_units) && !in_array($user->unit, $unit)){
                        unset($list_units[$key]);
                    }
                }
                $units = array_merge($units, $list_units);
            }
        }

        return view('survey.survey-detail', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'auth',
            'survey',
            'units',
            'unit_user'
        ));
    }

    public function store()
    {
        $survey = new Survey();
        $survey->created_by = $this->user_info['username'];
        $survey->unit = Input::get('unit');
        $survey->title = Input::get('title');
        $survey->is_subject = Input::get('is_subject');

        $survey->start_date = date('Y-m-d', strtotime(Input::get('start_date')));
        $survey->end_date = date('Y-m-d', strtotime(Input::get('end_date')));

        if(Input::get('is_subject')=='0'){
            foreach (Input::get('sample') as $key => $value) {
                $survey->$value = '1';
            }
        }else{
            $survey->student = '1';
        }

        DB::transaction(function () use ($survey){
            $saved_survey = $survey->save();
            if($saved_survey){
                $survey_objs = new Collection();

                foreach (Input::get('unit_objectives') as $key => $item){
                    $survey_obj = new SurveyObjective();
                    $survey_obj->objective = $item;

                    $survey_objs->push($survey_obj);
                }

                if(isset($survey_objs)){
                    $survey->surveyObjective()->saveMany($survey_objs);
                }

                $id_ = Input::get('id');
                if(isset($id_)){
                    $questions = Question::where('survey_id',$id_)->get();
                    foreach ($questions as $question){
                        $newQuestion = $question->replicate();
                        $newQuestion->survey_id = $survey->id;
                        $newQuestion->save();
                    }
                }
            }
        });

        echo "success";
    }

    public function show($id)
    {
        $survey = Survey::find($id);
        if (empty($survey))
        {
            return abort('404');
        }
        $survey->start_date = date('d-m-Y', strtotime($survey->start_date));
        $survey->end_date = date('d-m-Y', strtotime($survey->end_date));

        array_push($this->css['pages'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/css/datepicker.css');
        array_push($this->js['scripts'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/js/bootstrap-datepicker.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/jquery-validation/dist/jquery.validate.min.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/jquery.inputmask/dist/jquery.inputmask.bundle.min.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/jquery-ui.js');

        View::share('css', $this->css);
        View::share('js', $this->js);

        $upd_mode = 'show';
        $action_url = 'survey/edit';
        $page_title = 'Lihat Survei';
        $disabled = 'disabled';

        $survey->sample = new Collection();

        if(!is_null($survey->student)){
            $survey->sample->push('student');
        }
        if (!is_null($survey['lecture'])){
            $survey->sample->push('lecture');
        }
        if (!is_null($survey['employee'])){
            $survey->sample->push('employee');
        }

        $survey_objs = $survey->surveyObjective()->get();
        $unit_user = "";

        $user_auth = UserAuth::where('username',$this->user_info['username'])->get();
        $auth = null;

        if($user_auth->contains('auth_type','SU')){
            $auth = 'SU';
        }elseif($user_auth->contains('auth_type','OPU')){
            $auth = 'OPU';
        }else{
            $auth = 'OPF';
        }

        $simsdm = new Simsdm();
        $units = [];

        if($user_auth->contains('auth_type','SU')){
            $units = $simsdm->unitAll();
        }else{
            foreach ($user_auth as $user){
                $list_units = $simsdm->unitAll();

                foreach ($list_units as $key=>$unit){
                    if (is_array($list_units) && !in_array($user->unit, $unit)){
                        unset($list_units[$key]);
                    }
                }
                $units = array_merge($units, $list_units);
            }
        }

        return view('survey.survey-detail', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'auth',
            'survey',
            'survey_objs',
            'units',
            'unit_user'
        ));
    }

    public function edit($id)
    {
        $survey = Survey::find($id);
        if (empty($survey))
        {
            return abort('404');
        }
        $survey->start_date = date('d-m-Y', strtotime($survey->start_date));
        $survey->end_date = date('d-m-Y', strtotime($survey->end_date));

        array_push($this->css['pages'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/css/datepicker.css');
        array_push($this->js['scripts'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/js/bootstrap-datepicker.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/jquery-validation/dist/jquery.validate.min.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/jquery.inputmask/dist/jquery.inputmask.bundle.min.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/jquery-ui.js');

        View::share('css', $this->css);
        View::share('js', $this->js);

        $upd_mode = 'edit';
        $action_url = 'survey/edit';
        $page_title = 'Update Survey';
        $disabled = '';

        $survey->sample = new Collection();

        if(!is_null($survey->student)){
            $survey->sample->push('student');
        }
        if (!is_null($survey['lecture'])){
            $survey->sample->push('lecture');
        }
        if (!is_null($survey['employee'])){
            $survey->sample->push('employee');
        }

        $survey_objs = $survey->surveyObjective()->get();
        $unit_user = "";

        $user_auth = UserAuth::where('username',$this->user_info['username'])->get();
        $auth = null;

        if($user_auth->contains('auth_type','SU')){
            $auth = 'SU';
        }elseif($user_auth->contains('auth_type','OPU')){
            $auth = 'OPU';
        }else{
            $auth = 'OPF';
        }

        $simsdm = new Simsdm();
        $units = [];

        if($user_auth->contains('auth_type','SU')){
            $units = $simsdm->unitAll();
        }else{
            foreach ($user_auth as $user){
                $list_units = $simsdm->unitAll();

                foreach ($list_units as $key=>$unit){
                    if (is_array($list_units) && !in_array($user->unit, $unit)){
                        unset($list_units[$key]);
                    }
                }
                $units = array_merge($units, $list_units);
            }
        }

        return view('survey.survey-detail', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'auth',
            'survey',
            'survey_objs',
            'units',
            'unit_user'
        ));
    }

    public function update(StoreSurveyRequest $request)
    {
        $survey = Survey::find($request->id);
        $survey->created_by = $this->user_info['username'];
        $survey->unit = $request->unit;
        $survey->title = $request->title;
        $survey->is_subject = $request->is_subject;

        $survey->start_date = date('Y-m-d', strtotime($request->start_date));
        $survey->end_date = date('Y-m-d', strtotime($request->end_date));

        if($request->is_subject=='0'){
            foreach ($request->sample as $key => $value) {
                $survey->$value = '1';
            }
        }else{
            $survey->student = '1';
        }

        DB::transaction(function () use ($survey, $request){
            $saved_survey = $survey->save();
            if($saved_survey){
                $survey->surveyObjective()->delete();
                $survey_objs = new Collection();

                foreach ($request->unit_objectives as $key => $item){
                    $survey_obj = new SurveyObjective();
                    $survey_obj->objective = $item;

                    $survey_objs->push($survey_obj);
                }

                if(isset($survey_objs)){
                    $survey->surveyObjective()->saveMany($survey_objs);
                }
            }
        });

        $request->session()->flash('alert-success', 'Survei berhasil diubah');

        return redirect()->intended('/survey');
    }

    public function destroy()
    {
        $id = Input::get('id');
        $survey = Survey::find($id);
        $survey->surveyObjective()->delete();
        $survey->question()->delete();
        $survey->userAnswer()->delete();
        if(empty($survey))
        {
            return abort('404');
        }

        $saved = $survey->delete();
        if($saved)
            session()->flash('alert-success', 'Survei berhasil dihapus');
        else
            session()->flash('alert-danger', 'Terjadi kesalahan pada sistem, Survei gagal dihapus');

        return redirect()->intended('/');
    }

    public function answer($id)
    {
        array_push($this->css['pages'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/css/datepicker.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/bootstrap-daterangepicker/css/daterangepicker.css');

        array_push($this->js['scripts'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/js/bootstrap-datepicker.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/bootstrap-daterangepicker/js/daterangepicker.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/jquery-validation/dist/jquery.validate.min.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/jquery-ui.js');
        array_push($this->js['plugins'], 'js/loadingoverlay_progress.min.js');
        array_push($this->js['plugins'], 'js/loadingoverlay.min.js');

        View::share('css', $this->css);
        View::share('js', $this->js);

        $upd_mode = 'copy';
        $action_url = 'survey/answer';
        $page_title = 'Jawab Survei';
        $disabled = '';

        $questions = Question::where('survey_id',$id)->get();
        $answers = UserAnswer::with('question')->where('survey_id',$id)->where('username',$this->user_info['username'])->get();

        if(!$answers->isEmpty()){
            $disabled = 'disabled';
        }

        return view('survey.survey-answer', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'questions',
            'answers'
        ));
    }

    public function answerStore()
    {
        $data = Input::get('chosen');
        foreach (Input::get('qst_id') as $key => $value) {
            $user_answer = new UserAnswer();

            $type = Input::get('answer_type')[$key];

            $user_answer->username = $this->user_info['username'];
            $user_answer->survey_id = Input::get('survey_id');
            $user_answer->question_id = $value;
            $user_answer->answer_type = $type;
            $user_answer->subject_id = "";

            if($this->user_info['type'] == 1 || $this->user_info['type'] == 5){
                $user_answer->level= 'employee';
            }else{
                $user_answer->level= 'lecture';
            }

            if(isset($data) && $type=='2'){
                $val_chosen = "";
                $i = 0;
                $l = count($data[$value]);
                foreach($data[$value] as $a => $v) {
                    if($i == $l-1) {
                        $val_chosen.= $v;
                    }else{
                        $val_chosen.= $v.", ";
                    }
                    $i++;
                }
                $user_answer->answer = $val_chosen;
            }else{
                $answer = Input::get('answer')[$value];

                $user_answer->answer = $answer;
            }

            DB::transaction(function () use ($user_answer){
                $user_answer->save();
            });
        }

        echo "success";
    }

    public function copy($id)
    {
        array_push($this->css['pages'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/css/datepicker.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/bootstrap-daterangepicker/css/daterangepicker.css');

        array_push($this->js['scripts'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/js/bootstrap-datepicker.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/bootstrap-daterangepicker/js/daterangepicker.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/jquery-validation/dist/jquery.validate.min.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/jquery-ui.js');
        array_push($this->js['plugins'], 'js/loadingoverlay.min.js');


        View::share('css', $this->css);
        View::share('js', $this->js);

        $upd_mode = 'copy';
        $action_url = 'survey/create';
        $page_title = 'Salin Survei';
        $disabled = '';

        $survey = new Survey();
        $survey->id = $id;
        $survey->sample = new Collection();

        $user_auth = UserAuth::where('username',$this->user_info['username'])->get();
        $auth = null;

        if($user_auth->contains('auth_type','SU')){
            $auth = 'SU';
        }elseif($user_auth->contains('auth_type','OPU')){
            $auth = 'OPU';
        }else{
            $auth = 'OPF';
        }

        $simsdm = new Simsdm();
        $units = [];

        if($user_auth->contains('auth_type','SU')){
            $units = $simsdm->unitAll();
            $usu = array("id"=>"","code"=>"USU","name"=>"Universitas Sumatera Utara");
            array_push($units,$usu);
        }else{
            foreach ($user_auth as $user){
                $list_units = $simsdm->unitAll();

                foreach ($list_units as $key=>$unit){
                    if (is_array($list_units) && !in_array($user->unit, $unit)){
                        unset($list_units[$key]);
                    }
                }
                $units = array_merge($units, $list_units);
            }
        }

        return view('survey.survey-detail', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'auth',
            'survey',
            'units',
            'unit_user'
        ));
    }

    public function report()
    {
        dd($this->user_info);
        echo "asking survey will show report";
    }

    public function showreport()
    {
        echo "show report";
    }

    public function getAjax()
    {
        $user_auth = UserAuth::where('username',$this->user_info['username'])->first();

        if($user_auth->auth_type == 'SU'){
            $surveys = Survey::all();
        }else{
            $surveys = Survey::where('unit', $this->user_info['work_unit'])->get();
        }

        $data = [];
        $simsdm = new Simsdm();

        $i = 0;
        foreach ($surveys as $survey)
        {
            $sample='';
            if($survey->student=='1'){
                $sample = 'Mahasiswa ';
            }
            if($survey->lecture=='1'){
                $sample.= "Dosen ";
            }
            if($survey->employee=='1'){
                $sample.='Pegawai ';
            }

            $data['data'][$i][0] = $survey->id;
            $data['data'][$i][1] = $i + 1;
            $data['data'][$i][2] = $survey->title;

            $count_sample = UserAnswer::where('survey_id',$survey->id)->groupBy('username')->count();
            if($user_auth->auth_type == 'SU'){

                $list_units = $simsdm->unitAll();
                $usu = array("id"=>"","code"=>"USU","name"=>"Universitas Sumatera Utara");
                array_push($list_units,$usu);

                foreach ($list_units as $key=>$unit){
                    if (is_array($list_units) && !in_array($survey->unit, $unit)){
                        unset($list_units[$key]);
                    }
                }
                foreach ($list_units as $unit){
                    $survey->unit = $unit['name'];
                }

                $data['data'][$i][3] = $survey->unit;
                $data['data'][$i][4] = $survey->created_by;
                $data['data'][$i][5] = $sample;
                $data['data'][$i][6] = $count_sample;
                $data['data'][$i][7] = date('d M Y', strtotime($survey->start_date)). ' - '.date('d M Y', strtotime($survey->end_date));
            }else{
                $data['data'][$i][3] = $survey->created_by;
                $data['data'][$i][4] = $sample;
                $data['data'][$i][5] = $count_sample;
                $data['data'][$i][6] = date('d M Y', strtotime($survey->start_date)). ' - '.date('d M Y', strtotime($survey->end_date));
            }
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

    public function ajaxSurvey()
    {
        $data = [];
        $simsdm = new Simsdm();
        $i = 0;

        $type = $this->user_info['type'];
        $work_unit = $this->user_info['work_unit'];;

        if($type=='0' || $type=='1' || $type=='2' || $type=='3' || $type=='4'){
            $surveys = Survey::whereHas('SurveyObjective', function($q) use($work_unit) { $q->where('objective',$work_unit);})->where('lecture', '1')->get();
        }else if($type=='5'){
            $surveys = Survey::whereHas('SurveyObjective', function($q) use($work_unit) { $q->where('objective',$work_unit);})->where('employee', '1')->get();
        }else{
            $surveys = Survey::whereHas('SurveyObjective', function($q) use($work_unit) { $q->where('objective',$work_unit);})->where('student', '1')->get();
        }

        foreach ($surveys as $survey){
            $data['data'][$i][0] = $survey->id;
            $data['data'][$i][1] = $i + 1;
            $data['data'][$i][2] = $survey->title;

            $list_units = $simsdm->unitAll();
            $usu = array("id"=>"","code"=>"USU","name"=>"Universitas Sumatera Utara");
            array_push($list_units,$usu);

            foreach ($list_units as $key=>$unit){
                if (is_array($list_units) && !in_array($survey->unit, $unit)){
                    unset($list_units[$key]);
                }
            }

            foreach ($list_units as $unit){
                $survey->unit = $unit['name'];
            }

            $data['data'][$i][3] = $survey->unit;
            $data['data'][$i][4] = date('d M Y', strtotime($survey->start_date)). ' - '.date('d M Y', strtotime($survey->end_date));
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
}