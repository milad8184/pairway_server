<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\DateideaResource;
use App\Models\Answer;
use App\Models\Dateidea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DateideaController extends BaseController
{

    public function get($type)
    {

        $userid = $this->getUser()->id;
        $partnerRes = DB::select('SELECT CASE WHEN user1_id = ? THEN user2_id ELSE user1_id END AS partnerid FROM pair WHERE user1_id = ? OR user2_id = ?', [$userid, $userid, $userid]);
        $partnerid = 0;
        if (count($partnerRes) > 0) {
            $partnerid = $partnerRes[0]->partnerid;
        }
        $resp = DB::select('SELECT d.*, dl.created_at AS user_liked, dp.created_at AS partner_liked FROM dateidea d LEFT JOIN date_like dl ON d.id = dl.dateidea_id AND dl.user_id = ? LEFT JOIN date_like dp ON d.id = dp.dateidea_id AND dp.user_id = ? WHERE type = ? ORDER BY d.id', [$userid, $partnerid, $type]);
        return $this->sendResponse($resp);
    }

    public function update($id)
    {
        $userid = $this->getUser()->id;
        $resp = DB::table('date_like')->updateOrInsert(
            ['user_id' => $userid, 'dateidea_id' => $id],
            ['created_at' => DB::raw('IF(created_at IS NULL, NOW(), NULL)')]
        );
        return $this->sendResponse($resp);
    }

    public function destroy($id)
    {
        return $this->sendResponse(Dateidea::destroy($id));
    }
}
