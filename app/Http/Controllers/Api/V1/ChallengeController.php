<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChallengeController extends BaseController
{

    public function close(Request $request)
    {
        $validatedData = $request->validate([
            'image' => 'nullable|file|image', 
            'text' => 'nullable|string',       
            'challenge_id' => 'required', 
        ]);

        $pairId = $this->getUser()->pair_id;
        try {
            // Bild speichern (falls vorhanden)
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');

                $randomString = Str::random(32); // 32 Zeichen langer zufälliger String
                $extension = $image->getClientOriginalExtension(); // Ursprüngliche Dateierweiterung
                $fileName = $randomString . '.' . $extension;

                // Unterordner basierend auf pair_id

                // Speicherpfad erstellen
                $imagePath = $image->storeAs(
                    "challenge_results/$pairId", // Unterordner basierend auf pair_id
                    $fileName,
                    'public' // Speicherort: öffentliche Disk
                );
            }

          
            // Direkter Datenbankeintrag
           $resp =  DB::table('challenge_result')->insert([
                'challenge_url' => $imagePath ? Storage::url($imagePath) : null, // Konvertiert Pfad in URL
                'text' => $validatedData['text'],
                'challenge_id' => $validatedData['challenge_id'],
                'pair_id' =>  $pairId,
                'created_at' => now(),
            ]);

            return $this->sendResponse($resp);
        } catch (\Exception $e) {
            return $this->sendError($e);
        }
    }
}
