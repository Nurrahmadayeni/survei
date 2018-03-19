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
use App\Sia;
use App\User;
use App\UserAuth;
use View;
use Excel;
use PDF;
use parinpan\fanjwt\libs\JWTAuth;

class SurveyController extends MainController
{
    protected $simsdm;

    public function __construct()
    {
        $this->middleware('is_auth')->except('index','ajaxSurveyActive','reportExcel');
        $this->middleware('is_operator')->except('index', 'ajaxSurvey','ajaxSurveyActive','answer','answerStore','reportExcel');

        parent::__construct();

        $this->simsdm = new Simsdm();

        array_push($this->css['pages'], 'global/plugins/bower_components/fontawesome/css/font-awesome.min.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/animate.css/animate.min.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/jquery-ui/themes/base/jquery-ui.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/datatables/dataTables.bootstrap.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/datatables/datatables.responsive.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/select2/select2.min.css');
        array_push($this->css['pages'], 'global/plugins/bower_components/chosen_v1.2.0/chosen.min.css');

        array_push($this->js['plugins'], 'global/plugins/bower_components/datatables/jquery.dataTables.min.js');
         array_push($this->js['plugins'], 'global/plugins/bower_components/chosen_v1.2.0/chosen.jquery.min.js');
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
            $simsdm = new Simsdm();
            $login = new \stdClass();
            $login->logged_in = true;
            $login->payload = new \stdClass();
            $login->payload->user_unit = new \stdClass();
//             $login->payload->identity = env('USERNAME_LOGIN');
//             $login->payload->user_id = env('ID_LOGIN');

// //            $login->payload->identity = env('LOGIN_USERNAME');
// //            $login->payload->user_id = env('LOGIN_ID');

//             $user = $simsdm->getEmployee(env('USERNAME_LOGIN'));
//             $login->payload->name = $user->full_name;
//             $login->payload->logged_in_as = 1;
//             $login->payload->user_unit->photo = $user->photo;
//             $login->payload->user_unit->code = $user->work_unit;
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
            
            $page_title = 'Daftar Survei';

            $user_auth = UserAuth::where('username',$this->user_info['username'])->get();

            $auths = null;

            if($user_auth->contains('auth_type','SU') || $user_auth->contains('auth_type','SSU')){
                $auths = 'SU';
            }elseif($user_auth->contains('auth_type','OPU')){
                $auths = 'OPU';
            }elseif($user_auth->contains('auth_type','OPF')){
                $auths = 'OPF';
            }

            return view('survey.survey-list', compact('page_title', 'auths'));
        }
    }

    public function listAdmin(){
        $page_title = 'Daftar Survei Admin';

        $user_auth = UserAuth::where('username',$this->user_info['username'])->get();

        $auths = "admin";

        return view('survey.survey-list', compact('page_title', 'auths'));
    }

    public function create()
    {
        array_push($this->css['pages'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/css/datepicker.css');

        array_push($this->js['scripts'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/js/bootstrap-datepicker.js');
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

        if($user_auth->contains('auth_type','SU') || $user_auth->contains('auth_type','SSU')){
            $auth = 'SU';
        }elseif($user_auth->contains('auth_type','OPU')){
            $auth = 'OPU';
        }else{
            $auth = 'OPF';
        }

        $simsdm = new Simsdm();
        $units = [];

        if($user_auth->contains('auth_type','SU') || $user_auth->contains('auth_type','SSU')){
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

        $academic_years = new Collection();
        $start    = '2012-12-02';
        $end      = date("Y-m-d");
        $academic_years  = range(gmdate('Y', strtotime($start)), gmdate('Y', strtotime($end)));
        $academic_years = array_reverse($academic_years);

        return view('survey.survey-detail', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'auth',
            'survey',
            'units',
            'unit_user',
            'academic_years'
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
            $survey->academic_year = Input::get('academic_year');
            $survey->semester = Input::get('semester');
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

        if($user_auth->contains('auth_type','SU') || $user_auth->contains('auth_type','SSU')){
            $auth = 'SU';
        }elseif($user_auth->contains('auth_type','OPU')){
            $auth = 'OPU';
        }else{
            $auth = 'OPF';
        }

        $simsdm = new Simsdm();
        $units = [];

        if($user_auth->contains('auth_type','SU') || $user_auth->contains('auth_type','SSU')){
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

        $academic_years = new Collection();
        $start    = '2012-12-02';
        $end      = date("Y-m-d");
        $academic_years  = range(gmdate('Y', strtotime($start)), gmdate('Y', strtotime($end)));
        $academic_years = array_reverse($academic_years);

        return view('survey.survey-detail', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'auth',
            'survey',
            'survey_objs',
            'units',
            'unit_user',
            'academic_years'
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
        array_push($this->js['plugins'], 'js/loadingoverlay.min.js');

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

        if($user_auth->contains('auth_type','SU') || $user_auth->contains('auth_type','SSU')){
            $auth = 'SU';
        }elseif($user_auth->contains('auth_type','OPU')){
            $auth = 'OPU';
        }else{
            $auth = 'OPF';
        }

        $simsdm = new Simsdm();
        $units = [];

        if($user_auth->contains('auth_type','SU') || $user_auth->contains('auth_type','SSU')){
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

        $academic_years = new Collection();
        $start    = '2012-12-02';
        $end      = date("Y-m-d");
        $academic_years  = range(gmdate('Y', strtotime($start)), gmdate('Y', strtotime($end)));
        $academic_years = array_reverse($academic_years);

        return view('survey.survey-detail', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'auth',
            'survey',
            'survey_objs',
            'units',
            'unit_user',
            'academic_years'
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
        	$survey->student = null;
        	$survey->lecture = null;
        	$survey->employee = null;

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
        $sia = new Sia();
        $simsdm = new Simsdm();
        $survey = Survey::find($id);
        if(empty($survey)){
            return abort('404');
        }
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

        $questions = $survey->question()->get();

        $units = $simsdm->unitAll();

        $answers = UserAnswer::with('question')->where('survey_id',$id)->where('username',$this->user_info['username'])->get();

        if(!$answers->isEmpty()){
            $disabled = 'disabled';
        }

        $date = date('Y-m-d');
        if($date > $survey->end_date){
            $disabled = 'disabled';
            $deadline = true;
        }

        $subjects = [];

        if($survey->is_subject==1){
            $units = $simsdm->unitAll();
            foreach ($units as $unit){
                if($this->user_info['work_unit']==$unit['code']){
                    $unit_id = $unit['id'];
                }
            }

            $subjects = $sia->getSubject($unit_id,$this->user_info['username'])->MATAKULIAH;
            $semaktif = $survey->academic_year."".$survey->semester;

            foreach ($subjects as $key=>$subject){
                if ($subject->semester != $semaktif){
                    unset($subjects[$key]);
                }

                if($subject->semester == $semaktif){
                    $user_answers = $survey->userAnswer()->where('username',$this->user_info['username'])->
                    where('subject_id',$subject->kodemk)->first();

                    if(empty($user_answers)){
                        $subject->enable = "yes";
                    }else{
                        $subject->enable = "no";
                        $answers = UserAnswer::with('question')->where('survey_id',$id)->where('username',$this->user_info['username'])->where('subject_id',$subject->kodemk)->get();
                    }

                    if(empty($user_answers)){
                        $disabled = '';
                    }
                }
            }
        }

        return view('survey.survey-answer', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'questions',
            'answers',
            'deadline',
            'subjects'
        ));
    }

    public function answerStore()
    {
        $data = Input::get('chosen');
        $subject_id = Input::get('subject_id');

        if(!empty(Input::get('subject_id'))){
            $answer_exist = UserAnswer::where('survey_id',Input::get('survey_id'))->where('username',$this->user_info['username'])->where('subject_id',Input::get('subject_id'))->first();    
            if(!empty($answer_exist)){
                return ('exist');
            }
        }else{
            $answer_exist = UserAnswer::where('survey_id',Input::get('survey_id'))->where('username',$this->user_info['username'])->first();
            if(!empty($answer_exist)){
                return ('exist');
            }
        }
        

        $survey = Survey::find(Input::get('survey_id'));
        $question = $survey->question()->get();

        $user_answers = new Collection();


        foreach (Input::get('qst_id') as $key => $value) {

            $user_answer = new UserAnswer();

            $type = Input::get('answer_type')[$key];

            $user_answer->username = $this->user_info['username'];
            $user_answer->question_id = $value;
            $user_answer->answer_type = $type;

            if(!empty(Input::get('subject_id'))){
                $user_answer->subject_id = Input::get('subject_id');
            }else{
                $user_answer->subject_id = Input::get('subject_id');
            }

            $type = $this->user_info['type'];

            if($type=='0' || $type=='2' || $type=='3' || $type=='4'){
                $user_answer->level= 'lecture';
            }else if($type=='1' || $type=='5'){
                $user_answer->level= 'employee';
            }else{
                $user_answer->level= 'student';
            }


            $user_answer->unit = $this->user_info['work_unit'];

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

            $user_answers->push($user_answer);

        }

        if($user_answers->count() < $question->count()){
            return ('min');
        }elseif($user_answers->count() > $question->count()){
            return ('max');
        }elseif($user_answers->count() == $question->count()){
            if(isset($user_answers)){
                $survey->userAnswer()->saveMany($user_answers);
            }
            return ("success");
        }
    }

    public function showAnswer(){
        $subject_id = Input::get('subject_id');
        $survey_id = Input::get('survey_id');

        $answers = UserAnswer::with('question')->where('survey_id',$survey_id)->where('username',$this->user_info['username'])->where('subject_id',$subject_id)->get();
        if($answers->isEmpty()){
            echo "null";
        }else{
            $no=1;
            foreach($answers as $answer){
                $question = $answer->question['question'];
                $choices = $answer->question['choices'];
                $jawaban = $answer->answer;
                echo "
                    <div class='panel rounded shadow panel-theme' style='margin-bottom: 2%'>
                        <div class='panel-heading rounded' style='padding: 1%'>
                            $no. $question
                        </div>
                        <div class='panel-body' style='background-color:#F4F4F4'>";
                        if($answer->answer_type=='1'){
                            $val = explode(', ', $choices);
                
                            for($i=0; $i<=count($val)-1; $i++){
                                if($val[$i] == $answer->answer){
                                    $checked = 'checked';
                                }else{
                                    $checked = '';
                                }

                                echo "
                                <div class='rdio radio-inline rdio-theme rounded'>
                                    <input class='radio-inline' id='answerR$no$i' type='radio' value='$val[$i]' $checked disabled>
                                    <label for='answerR$no$i'>$val[$i]</label>
                                </div>";
                            }
                        } else if($answer->answer_type=='2'){

                            $val = explode(', ', $choices);
                            for($i=0; $i<=count($val)-1; $i++){
                                if(strpos( $answer->answer, $val[$i] ) !== false){
                                    $checked = 'checked'; 
                                }else{
                                    $checked = '';
                                }

                                echo "
                                <div class='ckbox ckbox-theme'>
                                    <input id='answerC$no$i' class='sampel' type='checkbox' value='$val[$i]' $checked disabled>
                                    <label for='answerC$no$i' class='control-label'>$val[$i]</label>
                                </div> ";
                            }

                        } else if($answer->answer_type=='3'){
                            echo "<input type='number' class='number form-control' value='$jawaban' disabled>";
                        }else{
                            echo "
                            <textarea class='form-control' rows='3' $disabled>$jawaban</textarea>
                            ";
                        }
                        echo "</div></div><hr>";
                    
                $no++; 
            }
        }
    }

    public function copy($id)
    {
        array_push($this->css['pages'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/css/datepicker.css');

        array_push($this->js['scripts'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/js/bootstrap-datepicker.js');
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

        if($user_auth->contains('auth_type','SU') || $user_auth->contains('auth_type','SSU')){
            $auth = 'SU';
        }elseif($user_auth->contains('auth_type','OPU')){
            $auth = 'OPU';
        }else{
            $auth = 'OPF';
        }

        $simsdm = new Simsdm();
        $units = [];

        if($user_auth->contains('auth_type','SU') || $user_auth->contains('auth_type','SSU')){
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

        $academic_years = new Collection();
        $start    = '2012-12-02';
        $end      = date("Y-m-d");
        $academic_years  = range(gmdate('Y', strtotime($start)), gmdate('Y', strtotime($end)));
        $academic_years = array_reverse($academic_years);

        return view('survey.survey-detail', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'auth',
            'survey',
            'units',
            'unit_user',
            'academic_years'
        ));
    }

    public function report()
    {
        array_push($this->js['plugins'], 'js/loadingoverlay.min.js');
        View::share('js', $this->js);

        $page_title = 'Laporan Survei';
        // $survey= Survey::all();
        $user_auth = UserAuth::where('username',$this->user_info['username'])->first();

        if($user_auth->auth_type == 'SU' || $user_auth->auth_type == 'SSU'){
            $survey = Survey::all();
        }else{
            $survey = Survey::where('unit', $user_auth->unit)->get();
        }

        return view('survey.survey-report', compact('page_title', 'survey'));
    }

    public function getObjective()
    {

        $input = Input::get('id');
        
        $survey = Survey::find($input);
        $survey_objective = $survey->surveyObjective()->get()->toArray();
        $simsdm = new Simsdm();

        $list_units = $simsdm->unitAll();
        $j = 0;
        $k = sizeof($survey_objective) - 1;

        foreach ($list_units as $key => $unit){
            if (empty($unit['code'])){
                unset($list_units[$key]);
            }
        }

        $unit_lists = [];

        foreach($survey_objective as $obj){
	        foreach ($list_units as $key=>$unit){
	            if (is_array($list_units) && in_array($obj['objective'], $unit)){
	                array_push($unit_lists, $unit);
	            }
	        }
    	}

        $all = array("id"=>"usu","code"=>"usu","name"=>"Semua Unit");
        array_unshift($unit_lists,$all);

        $data = json_encode($unit_lists, JSON_PRETTY_PRINT);

        return response($data, 200)->header('Content-Type', 'application/json');
    }
    
    public function showreport()
    {
        if(Input::get('survey_obj')=='usu'){
            $answer = UserAnswer::where('survey_id',Input::get('survey'))->first();
        }else{
            $answer = UserAnswer::where('survey_id',Input::get('survey'))->where('unit',Input::get('survey_obj'))->first();
        }

        if(!empty($answer)){
            echo "success";
        }
    }

    public function downloadReport()
    {
    	error_reporting(E_ALL);
    	ini_set("max_execution_time",360);

        $data = [];
        $datum = [];
        $simsdm = new Simsdm();
        $i = 0;
        $id1 = Input::get('id1');
        $survey = Survey::find(Input::get('id1'));
        $list_units = $simsdm->unitAll();


        if(Input::get('mode')==1){
            if(Input::get('id2')=='usu'){
                // $answers = UserAnswer::where('survey_id',Input::get('id1'))->orderBy('id')->skip(40500)->take(5400)->get();
                $answers = new Collection();

                $objective = $survey->surveyObjective()->get();

                $x = 0;
                foreach ($objective as $obj) {
                    $answers_temps = UserAnswer::where('survey_id',Input::get('id1'))->where('unit',$obj['objective'])->orderBy('username')->get();
                    if(!$answers_temps->isEmpty()){
                        foreach ($answers_temps as $answer) {
                            $answers->push($answer);    
                        }   
                    }               
                }
            
            }else{
                $answers = UserAnswer::where('survey_id',Input::get('id1'))->where('unit',Input::get('id2'))->get();
            }

            $prevIdent = null;
            $y = null;
            
            foreach($answers as $answer){
                foreach ($list_units as $key=>$unit){
                    if (is_array($list_units) && in_array($answer->unit, $unit)){
                        $answer->unit = $unit['name'];
                    }
                }

                if($answer->answer==1){
                    $answer->answer = "Tidak Puas";
                }elseif($answer->answer==2){
                    $answer->answer = "Kurang Puas";
                }elseif($answer->answer==3){
                    $answer->answer = "Biasa Saja";
                }elseif($answer->answer==4){
                    $answer->answer = "Puas";
                }elseif($answer->answer==5){
                    $answer->answer = "Sangat Puas";
                }

                if($answer->username==null){
                    $y = $i;
                    $prevIdent = $answer->username;
                    $data[$i]['No'] = $i+1;
                    $data[$i]['Identitas'] =  $answer->username;
                    $data[$i]['Unit'] = $answer->unit;
                    $data[$i]['Level'] = $answer->level;
                    $data[$y]['qst'][] = Question::find($answer->question_id)->question;
                    $data[$y]['answ'][] = $answer->answer;
                }

                if($answer->username==$prevIdent){    
                    
                    $data[$y]['answ'][] = $answer->answer;
                    $data[$y]['qst'][] = Question::find($answer->question_id)->question;
                }


                if($answer->username!=$prevIdent){
                    $prevIdent = $answer->username;
                    $data[$i]['No'] = $i+1;
                    $data[$i]['Identitas'] =  $answer->username;
                    $data[$i]['Unit'] = $answer->unit;
                    $data[$i]['Level'] = $answer->level;
                    // $data[$i]['qst_id'] = $answer->question_id;
                    $data[$i]['answ'][] = $answer->answer;
                    $data[$i]['qst'][] = Question::find($answer->question_id)->question;

                    $y = $i;
                }

                $i++;
            }
            // dd($data);
            $j = 0;
            foreach ($data as $dataa){
                $datum[$j]['No'] = $j + 1;
                $datum[$j]['Identitas'] = $dataa['Identitas'];
                $datum[$j]['Unit'] = $dataa['Unit'];
                $datum[$j]['Level'] = $dataa['Level'];
                $k=1;


                foreach ($dataa['qst'] as $key => $qst) {
                    $datum[$j][$qst] = $dataa['answ'][$key];
                }
                $j++;
            }

            return Excel::create('Report '.$survey->title, function($excel) use ($datum, $survey) {

                // Set the title
	            $excel->setTitle('Laporan Survei');

	            // Chain the setters
	            $excel->setCreator('PSI')
	                ->setCompany('PSI');

	            // Call them separately
	            $excel->setDescription('Laporan '.$survey->title);

	            $excel->sheet('Laporan', function ($sheet) use ($datum)
	            {
	                $sheet->fromArray($datum, null, 'A1', true);
	            });
            })->download('xls');

        }

        if(Input::get('mode')==2){
            $data = [];
            $i = 0;
            $survey_id = Input::get('id1');

            $unit = Input::get('id2');

            $survey = Survey::find($survey_id);
            
            if (empty($survey))
            {
                return abort('404');
            }
            $questions = $survey->question()->get();
            if (empty($questions))
            {
                return abort('404');
            }

            if($unit=='usu'){
                $sample_total = $survey->userAnswer()->groupBy('username')->get()->count();
            }else{
               $sample_total = $survey->userAnswer()->groupBy('username')->where('unit',$unit)->get()->count();
            }

            foreach ($questions as $question) {
                $choices = explode(', ', $question->choices);
                $data[$i]['question'] = $question->question;
                $j = 0;
                // $l = 0;
                for($q=0; $q<=count($choices)-1; $q++){
                    if($unit=='usu'){
                        $count = $question->userAnswer()->groupBy('username')->where('answer',$choices[$q])->get()->count();
                    }else{
                        $count = $question->userAnswer()->groupBy('username')->where('answer',$choices[$q])->where('unit',$unit)->get()->count();
                    }

                    $percentage = floatval(number_format((($count/$sample_total)*100),2));
                    if($choices[$q]==1){
                        $choices[$q] = "Tidak Puas";
                    }elseif($choices[$q]==2){
                        $choices[$q] = "Kurang Puas";
                    }elseif($choices[$q]==3){
                        $choices[$q] = "Biasa Saja";
                    }elseif($choices[$q]==4){
                        $choices[$q] = "Puas";
                    }elseif($choices[$q]==5){
                        $choices[$q] = "Sangat Puas";
                    }
                    $data[$i]['choices'][$j] = array($choices[$q], $percentage);
                    $j++;
                }
                $i++;
            }

            $list_units = $simsdm->unitAll();
            $usu = array("id"=>"","code"=>"usu","name"=>"Universitas Sumatera Utara");
            array_push($list_units,$usu);

            foreach ($list_units as $key=>$units){
                if (is_array($list_units) && !in_array($unit, $units)){
                    unset($list_units[$key]);
                }else{
                    $survey->unit = $units['name'];
                }
            }

        	// $pdf = PDF::loadView('survey.survey-report-pdf', compact('survey','data'));
            // return $pdf->download('Chart report survey.pdf');
            return view('survey.survey-report-pdf', compact('survey','data','sample_total'));
        }
    }

    public function getAjax()
    {
        $user_auth = UserAuth::where('username',$this->user_info['username'])->first();
        
        if($user_auth->auth_type == 'SU' || $user_auth->auth_type == 'SSU'){
            $surveys = Survey::all();
        }else{
            $surveys = Survey::where('unit', $user_auth->unit)->get();
        }

        $data = [];
        $simsdm = new Simsdm();

        $list_units = $simsdm->unitAll();
        $usu = array("id"=>"","code"=>"USU","name"=>"Universitas Sumatera Utara");
        array_push($list_units,$usu);


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

            $count = $users = DB::table('user_answers')->select('username')->where('survey_id',$survey->id)->groupBy('username')->get();
            $count_sample = $count->count();
            if($user_auth->auth_type == 'SU' || $user_auth->auth_type == 'SSU'){
               	foreach ($list_units as $key=>$unit){
					if($unit['code']==$survey->unit){
						$survey->unit = $unit['name'];
					}
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
        $sia = new Sia();
        $i = 0;

        $type = $this->user_info['type'];
        $work_unit = $this->user_info['work_unit'];

        if($type=='0' || $type=='2' || $type=='3' || $type=='4'){
            $surveys = Survey::whereHas('SurveyObjective', function($q) use($work_unit) { $q->where('objective',$work_unit);})->where('lecture', '1')->get();
        }else if($type=='1' || $type=='5'){
            $surveys = Survey::whereHas('SurveyObjective', function($q) use($work_unit) { $q->where('objective',$work_unit);})->where('employee', '1')->get();
        }else{
            $surveys = Survey::whereHas('SurveyObjective', function($q) use($work_unit) { $q->where('objective',$work_unit);})->where('student', '1')->get();
        }

        $list_units = $simsdm->unitAll();
        $usu = array("id"=>"","code"=>"USU","name"=>"Universitas Sumatera Utara");
        array_push($list_units,$usu);

        foreach ($surveys as $survey){
            $data['data'][$i][0] = $survey->id;
            $data['data'][$i][1] = $i + 1;
            $data['data'][$i][2] = $survey->title;
            if($survey->is_subject == 1){
                $data['data'][$i][2].= " <b> *Survei matakuliah </b>";
            }

            foreach ($list_units as $unit){
                if($survey->unit == $unit['code']){
                    $survey->unit = $unit['name'];
                }
            }

            $data['data'][$i][3] = $survey->unit;
            $data['data'][$i][4] = date('d M Y', strtotime($survey->start_date)). ' - '.date('d M Y', strtotime($survey->end_date));

            
            $status = $survey->userAnswer()->where('username',$this->user_info['username'])->first();
            if(empty($status)){
                $status = "Belum Mengisi Survei";
            }else{
                if($survey->is_subject == 0){
                    $status = "Telah Mengisi Survei";
                }else{
                    $units = $simsdm->unitAll();
                    foreach ($units as $unit){
                        if($this->user_info['work_unit']==$unit['code']){
                            $unit_id = $unit['id'];
                        }
                    }

                    $subjects = $sia->getSubject($unit_id,$this->user_info['username'])->MATAKULIAH;
                    $semaktif = $survey->academic_year."".$survey->semester;

                    foreach ($subjects as $key=>$subject){
                        if ($subject->semester == $semaktif){
                            $user_answers = $survey->userAnswer()->where('username',$this->user_info['username'])->
                            where('subject_id',$subject->kodemk)->first();

                            if(empty($user_answers)){
                                $status = "Anda belum menjawab sebagian survei matakuliah";
                                break;
                            }else{
                                $status = "Telah Mengisi Survei";
                            }
                        }
                    }
                }
            } 
            
            $data['data'][$i][5] = $status;
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

    public function ajaxSurveyActive()
    {
        $data = [];
        $simsdm = new Simsdm();
        $i = 0;
        $date = date('Y-m-d');
        $surveys = Survey::where('end_date','>=',$date)->get();

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
                }else{
                    $survey->unit = $unit['name'];
                }
            }

            $data['data'][$i][3] = $survey->unit;

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

            $data['data'][$i][4] = $sample;

            $survey_objective = $survey->surveyObjective()->get();
            if($survey_objective->contains('objective','USU')){
                $data['data'][$i][5] = "Universitas Sumatera Utara";
            }else
            {
                $objective = "";
                $list_units = $simsdm->unitAll();
                $j = 0;
                $k = $survey_objective->count() - 1;

                foreach ($list_units as $key => $unit)
                {
                    if (empty($unit['code']))
                    {
                        unset($list_units[$key]);
                    }
                }

                foreach ($list_units as $key => $unit)
                {
                    if (in_array($survey_objective[$j]->objective, $unit)) {
                        $objective.="<li>".$unit['name']."</li>";
                    }

                    if($j<$k){
                        $j++;
                    }
                }

                $data['data'][$i][5] = $objective;
            }

            $data['data'][$i][6] = date('d M Y', strtotime($survey->start_date)). ' - '.date('d M Y', strtotime($survey->end_date));
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

    public function reportExcel(){
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=Persentase Hasil Survei.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        $simsdm = new Simsdm();
        $list_units = $simsdm->unitAll();

        foreach ($list_units as $key=>$unit){
            if ($unit['type_str']!='Fakultas'){
                unset($list_units[$key]);
            }
            if($unit['code']=='TESTFAK'){
                unset($list_units[$key]);
            }
        }

        echo "NO \t FAKULTAS \t Jumlah jawab survey \t Total mahasiswa \t Persen yang telah menjawab \n";
        $sum_fac = [6291, 4703, 4163, 5489, 7093, 1977, 5214, 4251, 4619, 3598, 1588, 1082, 1805, 1108, 838, 2133];
        $i = 0;
        foreach ($list_units as $unit){
            $j = $i+1;
            echo $j."\t";
            echo $unit['name']. "\t";
            $count = DB::table('user_answers')->select('username')->where('unit',$unit['code'])
            		->where('survey_id',3)->groupBy('username','unit')->get();
            $count_sample = $count->count();
            $sum_percent = ($count_sample / $sum_fac[$i]) * 100;
            echo $count_sample."\t".$sum_fac[$i]."\t";
            echo $count_sample."\t".$sum_fac[$i]."\t";
            echo number_format($sum_percent,2)."%";
            echo "\n";

            $i++;
        }
    }

    public function getDetail($user_id){
        $this->client = new \GuzzleHttp\Client();
        $response = $this->client->get('https://api.usu.ac.id/1.0/users/'.$user_id);
        $employee = json_decode($response->getBody());

        return $employee;
    }

    public function insert(){
        $this->client = new \GuzzleHttp\Client();
        $response = $this->client->get('https://api.usu.ac.id/0.1/units/20');

        $employees = json_decode($response->getBody());
        // dd($employees->data->officials);

        $count = 0;
        $delete = 0;
        foreach ($employees->data->officials as $user) { 
            $employee = $this->getDetail($user->user_id);
            
            if(isset($employee->nip)){
                $answer = UserAnswer::where('username', $employee->nip)->where('unit', $employee->work_unit)->first();

                    if(empty($answer)){
                        echo $employee->nip."<br/>";
                        $answer_user = new UserAnswer();
                        $answer_user->username = $employee->nip;
                        $answer_user->survey_id = 1;
                        $answer_user->question_id = 1;
                        $answer_user->answer_type = 1;
                        $answer_user->subject_id = "";
                        $answer_user->unit = $employee->work_unit;
                        $answer_user->level = "leader";
                        $answer_user->answer = "Pernah";
                        $answer_user->created_at = "2018-01-25 22:42:57";
                        $answer_user->updated_at = "2018-01-25 22:42:57";

                        $answer_user->save();

                        $answer_user = new UserAnswer();
                        $answer_user->username = $employee->nip;
                        $answer_user->survey_id = 1;
                        $answer_user->question_id = 2;
                        $answer_user->answer_type = 1;
                        $answer_user->subject_id = "";
                        $answer_user->unit = $employee->work_unit;
                        $answer_user->level = "leader";
                        $answer_user->answer = "Pernah";
                        $answer_user->created_at = "2018-01-25 22:42:57";
                        $answer_user->updated_at = "2018-01-25 22:42:57";

                        $answer_user->save();

                        $answer_user = new UserAnswer();
                        $answer_user->username = $employee->nip;
                        $answer_user->survey_id = 1;
                        $answer_user->question_id = 3;
                        $answer_user->answer_type = 1;
                        $answer_user->subject_id = "";
                        $answer_user->unit = $employee->work_unit;
                        $answer_user->level = "leader";
                        $answer_user->answer = "Pernah";
                        $answer_user->created_at = "2018-01-25 22:42:57";
                        $answer_user->updated_at = "2018-01-25 22:42:57";

                        $answer_user->save();

                        $answer_user = new UserAnswer();
                        $answer_user->username = $employee->nip;
                        $answer_user->survey_id = 1;
                        $answer_user->question_id = 4;
                        $answer_user->answer_type = 1;
                        $answer_user->subject_id = "";
                        $answer_user->unit = $employee->work_unit;
                        $answer_user->level = "lecture";
                        $answer_user->answer = "Website USU";
                        $answer_user->created_at = "2018-01-25 22:42:57";
                        $answer_user->updated_at = "2018-01-25 22:42:57";

                        $answer_user->save();

                        $answer_user = new UserAnswer();
                        $answer_user->username = $employee->nip;
                        $answer_user->survey_id = 1;
                        $answer_user->question_id = 5;
                        $answer_user->answer_type = 1;
                        $answer_user->subject_id = "";
                        $answer_user->unit = $employee->work_unit;
                        $answer_user->level = "leader";
                        $answer_user->answer = "Sangat Paham";
                        $answer_user->created_at = "2018-01-25 22:42:57";
                        $answer_user->updated_at = "2018-01-25 22:42:57";

                        $answer_user->save();

                        $answer_user = new UserAnswer();
                        $answer_user->username = $employee->nip;
                        $answer_user->survey_id = 1;
                        $answer_user->question_id = 6;
                        $answer_user->answer_type = 1;
                        $answer_user->subject_id = "";
                        $answer_user->unit = $employee->work_unit;
                        $answer_user->level = "leader";
                        $answer_user->answer = "Sangat Paham";
                        $answer_user->created_at = "2018-01-25 22:42:57";
                        $answer_user->updated_at = "2018-01-25 22:42:57";

                        $answer_user->save();
                        $count++;
                    }else{
                        // $answer->where('username',$employee->nip)->delete($answer->id);
                        // $delete++;
                    }
            }
        }
        echo "jumlah yang bertambah :".$count."\n";
        echo "jumlah yang berkurang :".$delete;
    }
}