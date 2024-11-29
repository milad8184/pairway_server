<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Resources\AnswerResource;
use App\Models\Answer;
use App\Models\LoveLanguage;
use App\Models\LoveLanguageUserAnswer;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoveLanguagesController extends BaseController
{
    /**
     * Display a listing of the resource.
     */


    public function get()
    {
        $user = $this->getUser();
        $id = $user->id;
        $LLResult = DB::select('SELECT love_language_result.*,love_language.name_de,love_language.name_en,love_language.text_de,love_language.text_en FROM love_language_result LEFT JOIN love_language ON (love_language_result.love_language_id = love_language.id)  WHERE love_language_result.user_id=?', [$id]);
        $partnerLLResult = DB::select('SELECT love_language_result.*,user.name, love_language.name_de, love_language.name_en, love_language.text_de, love_language.text_en FROM love_language_result LEFT JOIN love_language ON love_language_result.love_language_id = love_language.id JOIN pair AS p ON (love_language_result.user_id = p.user1_id AND p.user2_id = ?) OR (love_language_result.user_id = p.user2_id AND p.user1_id = ?) LEFT JOIN user ON (love_language_result.user_id = user.id) WHERE p.user1_id = ? OR p.user2_id = ?', [$id, $id, $id, $id]);

        $activeSubscription = Subscription::where('pair_id', $user->pair_id)
            ->where('status', 'active')
            ->where('end_date', '>=', Carbon::today())
            ->first();
        $subscriptionIsActive = false;
        if ($activeSubscription) {
            $subscriptionIsActive = true;
        }
        $resp = [
            "llResult" => $LLResult,
            "partnerLLResult" => $partnerLLResult,
            "subscriptionIsActive" => $subscriptionIsActive
        ];
        return $this->sendResponse($resp);
    }

    public function getQuestionsWithAnswers()
    {
        $user = $this->getUser();

        $questions = DB::table('love_language_question')->get();
        $answers = DB::table('love_language_answer')->get()
            ->groupBy('question_id');

        $result = [];
        foreach ($questions as $question) {
            $result[] = [
                'id' => $question->id,
                'text_de' => $question->text_de,
                'text_en' => $question->text_en,
                'answers' => $answers->get($question->id) ?? []
            ];
        }

        return $this->sendResponse($result);
    }

    public function saveLoveLanguagesAnswers(Request $request)
    {


        $userId = $this->getUser()->id; // Hole die Benutzer-ID
        // Lösche vorher alle Einträge des Benutzers
        DB::table('love_language_user_answer')->where('user_id', $userId)->delete();

        $answers = $request["answers"];
        foreach ($answers as $answer) {
            LoveLanguageUserAnswer::create([
                'user_id' => $userId,
                'question_id' => $answer['question_id'],
                'answer_id' => $answer['answer_id'],
            ]);
        }

        $topTwo = $request["top_two"];
        $ids = array_map(function ($entry) {
            return $entry['love_language_id'];
        }, $topTwo);

        $loveLanguages = LoveLanguage::whereIn('id', $ids)->get();
        return $this->sendResponse($loveLanguages);
    }

    public function saveLoveLanguagesResult(Request $request)
    {

        $userId = $this->getUser()->id;
        DB::table('love_language_result')->where('user_id', $userId)->delete();

        $shareresult = $request["shareresults"];
        $shareanswers = $request["shareanswers"];
        $topTwo = $request["topTwo"];
        $insertData = [];
        foreach ($topTwo as $t) {
            $insertData[] = [
                'user_id' => $userId,
                'love_language_id' => $t["love_language_id"],
                'score' => $t["score"],
                'shareanswers' => $shareanswers,
                'shareresult' => $shareresult,
            ];
        }
        $resp = DB::table('love_language_result')->insert($insertData);
        return $this->sendResponse($resp);
    }

    public function updateLoveLanguagesResult(Request $request)
    {
        $userId = $this->getUser()->id;
        $shareresult = $request["shareresults"];
        $shareanswers = $request["shareanswers"];
        $resp = DB::table('love_language_result')->where('user_id', $userId)->update(['shareresult' => $shareresult, 'shareanswers' => $shareanswers]);
        return $this->sendResponse($resp);
    }


    public function destroy($id)
    {
        return $this->sendResponse(Answer::destroy($id));
    }
}
