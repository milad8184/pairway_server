<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\FirebaseNotificationService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends BaseController
{

    protected $firebaseNotificationService;

    public function __construct(FirebaseNotificationService $firebaseNotificationService)
    {
        $this->firebaseNotificationService = $firebaseNotificationService;
    }

    public function index()
    {
        $companyid = Auth()->user()->companyid;
        $users = DB::select('SELECT * FROM users WHERE companyid = ? AND application = 0', [$companyid]);
        $users = UserResource::collection($users);
        return $this->sendResponse($users);
    }

    public function pushnotification()
    {
        $target = "eRu09YjtQIi0NS9fP1Qn01:APA91bGMue1ohGiAV3q3cGQta_DqSg8cG6SXpdSK3T_SukyWf3EMJnsYcgujzKDFuqGtV8n7HcxLdApVk0x9NZNFvW5hk4hZVt11S9FvDjbzqXdFEVvu9o4";
        $title = 'title';
        $body = 'body';
        //        $data = $request->input('data', []);
        $data = [];
        $result = $this->firebaseNotificationService->sendPushNotification($target, $title, $body, $data);

        return response()->json(['message' => $result]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request)
    {

        $jsonData = $request->all();
        $validatedData = Validator::make($jsonData, [
            'name' => 'required|string',
            'email' => 'required|string', // string statt username
            'password' => 'nullable|string',
            'gender' => 'required|string',
        ])->validate();

        $user = $jsonData;
        $uuid = str_replace('-', '', Str::uuid());
        $user["uuid"] = $uuid;
        $user["connectkey"] = $this->generateUniqueConnectKey(); // Generiere den einzigartigen Key
        if (!empty($request['nativeid']) && !empty($request['type'])) {
            $user["nativeid"] = $request['nativeid'];
            $user["type"] = $request['type'];
            $user["photoUrl"] = $request['photoUrl'];
            unset($user["password"]);
        } else {
            $user["password"] = Hash::make($validatedData['password']);
        }
        $newUser = User::create($user);
        /*  if ($pairId != 0) {
            try {
                $pair = Pair::findOrFail($pairId);
            } catch (Exception $e) {
                return $this->sendError("pairNotFound");
            }
            $user["pair_id"] = $pairId;
            $newUser = User::create($user);
            if ($pair != NULL) {
                $pair->update(["user2_id" => $newUser->id]);
            }
        } else { */

        /* $newPair = [];
        $newPair["user1_id"] = $newUser->id;
        $newPair["uuid"] = str_replace('-', '', Str::uuid());
        $newPair["created_at"] = now();
        $newPair["connectkey"] = $this->generateUniqueConnectKey(); // Generiere den einzigartigen Key
        $pair = Pair::create($newPair);
        $newUser->update(["pair_id" => $pair->id]); */

        //  }

        $resp = [
            "user" => UserResource::make($newUser)
            //      "pair" => PairResource::make($pair)
        ];
        return $this->sendResponse($resp);
    }

    private function generateUniqueConnectKey()
    {
        do {
            $key = strtoupper(Str::random(6));
            $key = substr(preg_replace('/[^A-Z]/', '', $key), 0, 6);

            while (strlen($key) < 6) {
                $key .= strtoupper(Str::random(6 - strlen($key)));
                $key = substr(preg_replace('/[^A-Z]/', '', $key), 0, 6);
            }
        } while (User::where('connectkey', $key)->exists());

        return $key;
    }

    public function show($id)
    {

        $user = User::find($id);
        if (!$this->validateCompany($user->companyid)) {
            return $this->sendError("notAllowed");
        }
        if (!$this->hasUserAuthority($id)) {
            return $this->sendError("notAllowed");
        }

        $orders = DB::select('SELECT orders.*, products.imageurl,products.thc,products.description FROM orders LEFT JOIN products ON (orders.productid = products.id)  WHERE orders.userid=? ORDER BY orders.created_at', [$id]);
        $resp = [
            "info" => UserResource::make($user),
            "orders" => $orders
        ];
        return $this->sendResponse($resp);
    }

    public function update(Request $request, $id)
    {

        $user = User::findOrFail($id);
        $user->update($request->all());
        $resp = UserResource::make($user);
        return $this->sendResponse($resp);
    }

    public function destroy(User $user)
    {
        User::destroy($user->id);
    }
}
