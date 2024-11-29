<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Resources\AnswerResource;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends BaseController
{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {
        $id = Auth()->user()->id;
        return $this->sendResponse([]);
    }

    public function get($type)
    {
        $userid = $this->getUser()->id;
        $partnerRes = DB::select('SELECT CASE WHEN user1_id = ? THEN user2_id ELSE user1_id END AS partnerid FROM pair WHERE user1_id = ? OR user2_id = ?', [$userid, $userid, $userid]);
        $partnerid = 0;
        if (count($partnerRes) > 0) {
            $partnerid = $partnerRes[0]->partnerid;
        }
        $resp = DB::select('SELECT q.*, a.answer_text AS user_answer, pa.answer_text AS partner_answer FROM question q LEFT JOIN answer a ON q.id = a.question_id AND a.user_id = ? LEFT JOIN answer pa ON q.id = pa.question_id AND pa.user_id = ? WHERE type = ? ORDER BY q.id', [$userid, $partnerid, $type]);
        return $this->sendResponse($resp);
    }

    public function store(Request $request)
    {
        $answer = $request->all();
        $answer["user_id"] = Auth()->user()->id;
        $resp = AnswerResource::make(Answer::create($answer));
        return $this->sendResponse($resp);
    }


    public function destroy($id)
    {
        return $this->sendResponse(Answer::destroy($id));
    }
}
