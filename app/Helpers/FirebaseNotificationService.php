<?php

namespace App\Helpers;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Exception;

class FirebaseNotificationService
{
    protected $firebase;

    public function __construct()
    {
        $serviceAccountPath = __DIR__ . '/service.json';

        $this->firebase = (new Factory)
            ->withServiceAccount($serviceAccountPath); // Hole den Pfad aus der .env Datei
    }

    /**
     * Sendet eine Push-Nachricht an ein Gerät oder Topic
     *
     * @param string $target Empfänger-Token oder Topic-Name
     * @param string $title Titel der Push-Nachricht
     * @param string $body Inhalt der Push-Nachricht
     * @param array $data Zusätzliche Daten (optional)
     * @return string Rückgabe des Erfolgs oder Fehlers
     */
    public function sendPushNotification(string $target, string $title, string $body, array $data = [])
    {
        try {
            $messaging = $this->firebase->createMessaging();

            // Erstelle die Benachrichtigung
            $notification = Notification::create($title, $body);

            // Baue die Nachricht
            $message = CloudMessage::withTarget('token', $target)  // Nutze 'token' für Gerät oder 'topic' für Topic
                ->withNotification($notification)
                ->withData($data); // Zusätzliche Daten

            // Sende die Nachricht
            $messaging->send($message);

            return "Push-Nachricht erfolgreich gesendet!";
        } catch (Exception $e) {
            return 'Fehler: ' . $e->getMessage();
        }
    }
}