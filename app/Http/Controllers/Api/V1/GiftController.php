<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\DateideaResource;
use App\Http\Resources\PairResource;
use App\Models\Answer;
use App\Models\Dateidea;
use App\Models\Pair;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiftController extends BaseController
{

    public function get()
    {

        $resp = DB::select('SELECT * FROM gift');
        return $this->sendResponse($resp);
    }

    public function addGift(Request $request)
    {
        $user = $this->getUser();
        $points = $request->points;
        $giftid = $request->giftid;
        if ($user->points < $points) {
            return $this->sendError("notEnoughPoints");
        }

        $pair = Pair::find($user->pair_id);
        $data['pair'] = PairResource::make($pair);
        $partnerId = $pair->user1_id == $user->id ? $pair->user2_id : $pair->user1_id;
        $data = [
            'fromuserid' => $user->id,
            'touserid' => $partnerId,
            'giftid' => $giftid,
        ];
        $resp = DB::table('gift_history')->insert($data);
        if ($resp == true) {
            $resp = User::where('id', $user->id)->decrement('points', $points);
            return $this->sendResponse($resp);
        } else {
            return $this->sendError("error");
        }
    }

    public function destroy($id)
    {
        return $this->sendResponse(Dateidea::destroy($id));
    }
}
