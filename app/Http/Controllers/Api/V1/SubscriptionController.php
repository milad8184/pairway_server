<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends BaseController
{
    /**
     * Zeigt alle Abonnements an.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Alle Abonnements abrufen
        $subscriptions = Subscription::all();
        return $this->sendResponse($subscriptions);
    }

    /**
     * Erstellt ein neues Abonnement.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validierung der Eingabedaten
        $validator = Validator::make($request->all(), [
            'subscription_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $this->getUser();

        // Neues Abonnement erstellen
        $subscription = Subscription::create([
            'subscription_type' => $request->subscription_type,
            'status' => 'active', // Standardstatus
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'pair_id' => $user->pair_id
        ]);

        return $this->sendResponse($subscription);
    }

    /**
     * Zeigt ein einzelnes Abonnement an.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subscription = Subscription::with('users')->findOrFail($id);
        return $this->sendResponse($subscription);
    }

    /**
     * Aktualisiert den Status eines Abonnements.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        // Validierung der Status-Eingabe
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive,expired',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Abonnement abrufen und Status aktualisieren
        $subscription = Subscription::findOrFail($id);
        $subscription->status = $request->status;
        $subscription->save();

        return $this->sendResponse($subscription);
    }

    /**
     * Löscht ein Abonnement.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();
        return $this->sendResponse(['message' => 'Abonnement erfolgreich gelöscht']);
    }
}
