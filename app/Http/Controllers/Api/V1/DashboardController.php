<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{

    public function get()
    {
        $startsec = microtime(true);
        $user = $this->getUser();
        $id = $user->id;
        $quotecount = 5;
        $challengecount = 1;
        $challenge = NULL;
        $LLResult = DB::select('SELECT love_language_result.*,love_language.name_de,love_language.name_en,love_language.text_de,love_language.text_en FROM love_language_result LEFT JOIN love_language ON (love_language_result.love_language_id = love_language.id)  WHERE love_language_result.user_id=?', [$id]);
        $partnerLLResult = DB::select('SELECT love_language_result.*,user.name, love_language.name_de, love_language.name_en, love_language.text_de, love_language.text_en FROM love_language_result LEFT JOIN love_language ON love_language_result.love_language_id = love_language.id JOIN pair AS p ON (love_language_result.user_id = p.user1_id AND p.user2_id = ?) OR (love_language_result.user_id = p.user2_id AND p.user1_id = ?) LEFT JOIN user ON (love_language_result.user_id = user.id) WHERE p.user1_id = ? OR p.user2_id = ?', [$id, $id, $id, $id]);
        $dailyquote = DB::select('SELECT * FROM lovequote WHERE id = ((DAYOFYEAR(CURDATE()) - 1) % ?) + 1', [$quotecount]);
        if ($user->pair_id != NULL) {
            $challengeRes = DB::select('SELECT challenge.*, challenge_result.text,challenge_result.challenge_url,challenge_result.created_at FROM challenge LEFT JOIN challenge_result ON challenge.id = challenge_result.challenge_id AND challenge_result.pair_id = ? WHERE challenge.id = ((WEEKOFYEAR(CURDATE()) - 1) % ?) + 1', [$user->pair_id, $challengecount]);
            $challenge = $challengeRes[0];
        }
        $time = (microtime(true) - $startsec) * 1000;
        $resp = [
            "llResult" => count($LLResult) > 0,
            "partnerLLResult" => count($partnerLLResult) > 0,
            "time" => $time,
            "dailyquote" => $dailyquote[0],
            "challenge" => $challenge,
            "points" => $user->points,
            "partnerId" => $user->partnerId
        ];

        return $this->sendResponse($resp);
    }
}
