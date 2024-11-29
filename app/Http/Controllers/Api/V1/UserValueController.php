<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;

class UserValueController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companyid = Auth()->user()->companyid;
        $userStats = DB::select('SELECT count(id) as count,(AVG(DATEDIFF(DATE(NOW()), DATE(birthday))) / 365) as age, (SELECT count(id) FROM users WHERE companyid =? AND gender = "m") as males ,(SELECT count(id) FROM users WHERE companyid =? AND gender = "w") as females ,(SELECT count(id) FROM users WHERE companyid =? AND gender = "d") as divers FROM users WHERE companyid=?', [$companyid,$companyid,$companyid,$companyid]);
        //$companyStats = DB::select('SELECT count(id) as count,SUM(productprice) as price, SUM(quantity) as quantity FROM orderitem WHERE companyid=?', [$companyid]);
        $data = ["memberStats" => $userStats[0]];
        return $this->sendResponse($data);
    }
}
