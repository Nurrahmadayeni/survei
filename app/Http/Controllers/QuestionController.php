<?php

namespace App\Http\Controllers;

use App\AnswerType;
use App\Http\Requests\StoreQuestionRequest;
use App\Question;
use App\Simsdm;
use App\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;
use View;
use parinpan\fanjwt\libs\JWTAuth;

class QuestionController extends MainController
{
    protected $simsdm;

    public function __construct(){
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
            $page_title = 'Daftar Pertanyaan';

            return view('question.question-list', compact('page_title'));
        }
    }

    public function create($id)
    {
        $survey = Survey::find($id);
        if (empty($survey))
        {
            return abort('404');
        }
        $survey->start_date = date('d-m-Y', strtotime($survey->start_date));
        $survey->end_date = date('d-m-Y', strtotime($survey->end_date));
        $question_total = Question::where('survey_id',$id)->count() + 1;

        array_push($this->css['pages'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/css/datepicker.css');
        array_push($this->js['scripts'], 'global/plugins/bower_components/bootstrap-datepicker-vitalets/js/bootstrap-datepicker.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/jquery-validation/dist/jquery.validate.min.js');
        array_push($this->js['scripts'], 'global/plugins/bower_components/jquery.inputmask/dist/jquery.inputmask.bundle.min.js');
        array_push($this->js['plugins'], 'global/plugins/bower_components/jquery-ui/jquery-ui.js');
        array_push($this->js['plugins'], 'js/loadingoverlay.min.js');

        View::share('css', $this->css);
        View::share('js', $this->js);

        $upd_mode = 'show';
        $action_url = 'question/create';
        $page_title = 'Tambah Pertanyaan';
        $disabled = '';

        $types = AnswerType::get();

        return view('question.question-detail', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'survey',
            'question_total',
            'types'
        ));
    }

    public function store()
    {
        $question = new Question();
        $question->survey_id = Input::get('survey_id');
        $question->question = Input::get('question');
        $question->answer_type = Input::get('answer_type');

        $choices = Input::get('choices');
        $value_chosen = "";

        if(isset($choices)){
            for($i=0; $i<count($choices); $i++){
                if($i==count($choices)-1){
                    $value_chosen.=ucwords($choices[$i]);
                }else{
                    $value_chosen.=ucwords($choices[$i]).', ';
                }
            }
        }
        $question->choices = $value_chosen;

        DB::transaction(function () use ($question){
            $question->save();
        });

        echo "success";
    }

    public function getQstTotal()
    {
        $question_total = Question::where('survey_id',Input::get('survey_id'))->count() + 1;
        echo $question_total;
    }

    public function getSampleTotal(Request $request)
    {
        $input = $request->all();

        $count = $users = DB::table('user_answers')->select('username')->where('survey_id',$input['survey_id'])->where('unit',$input['obj'])->groupBy('username')->get();
        $sample = $count->count();

        echo $sample;
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
        $action_url = 'question/show';
        $page_title = 'Daftar Pertanyaan';
        $disabled = '';

        $simsdm = new Simsdm();
        $list_units = $simsdm->unitAll();
        $usu = array("id"=>"","code"=>"USU","name"=>"Universitas Sumatera Utara");
        array_push($list_units,$usu);

        $survey_objective = $survey->surveyObjective()->get();
        $j = 0;
        $k = $survey_objective->count() - 1;

        foreach ($list_units as $key => $unit)
        {
            if (empty($unit['code']))
            {
                unset($list_units[$key]);
            }
        }

        foreach ($list_units as $key=>$unit){
            if (is_array($survey_objective) && !in_array($survey_objective[$j]->objective, $unit['code'])) {
                unset($list_units[$key]);
            }

            if($j<$k){
                $j++;
            }
        }

        $count = $users = DB::table('user_answers')->select('username')->where('survey_id',$id)->where('unit',$survey_objective[0]->objective)->groupBy('username')->get();
        $sample = $count->count();

        return view('question.question-list', compact(
            'upd_mode',
            'action_url',
            'page_title',
            'disabled',
            'survey',
            'list_units',
            'sample'
        ));
    }

    public function getAjax()
    {
        // dd(Input::get());
        $id = Input::get('id');
        $obj = Input::get('obj');
        $questions = Question::where('survey_id', $id)->get();

        $data = [];

        $i = 0;
        foreach ($questions as $question)
        {
            $answerType = $question->answerType()->first();
            $data['data'][$i][0] = $i + 1;
            $data['data'][$i][1] = $question->question;
            $data['data'][$i][2] = $answerType->type;
            $data['data'][$i][3] = $question->choices;
            // $data['data'][$i][4] = '<a data-id1="'.$id.'" data-id2="'.$question->id.'" data-id3="'.$obj.'" class="btn btn-theme btn-sm rounded diagram" data-toggle="tooltip" data-placement="top" title="Lihat Diagram"><i class="fa fa-eye" style="color:white;"></i></a>';
            $data['data'][$i][4] = '<a data-id1="'.$question->id.'" data-id2="'.$question->question.'" class="btn btn-theme btn-sm rounded edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-pencil" style="color:white;"></i></a>';
            $data['data'][$i][4].= '<a data-toggle="tooltip" data-placement="top" title="Delete"><button class="btn btn-danger btn-sm rounded delete" data-id="'.$question->id.'" data-toggle="modal" data-target="#delete"><i class="fa fa-times"></i></button></a>';
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

    public function edit()
    {
        $question = Question::find(Input::get('qst_id'));

        $question->question = Input::get('question');

        DB::transaction(function () use ($question){
            $question->save();
        });

        session()->flash('alert-success', 'Pertanyaan berhasil diubah');

        return redirect()->back();
    }

    public function destroy()
    {
        $id = Input::get('id');
        $question = Question::find($id);

        if(empty($question))
        {
            return abort('404');
        }

        $saved = $question->delete();
        if($saved)
            session()->flash('alert-success', 'Pertanyaan berhasil dihapus');
        else
            session()->flash('alert-danger', 'Terjadi kesalahan pada sistem, Pertanyaan gagal dihapus');

        return redirect()->back();
    }
}
