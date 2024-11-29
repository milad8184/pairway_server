<?php

namespace App\Helpers;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class Mailer extends Mailable
{
    public function sendInvatation($email,$company)
    {
        $userEmail = $email; //'milad1984@gmail.com';
        $subject = 'Mitglieder-Einladung für den Club ' . $company->name;
        $link = "https://greenclub.de/register/" . $company->uuid;
        $messageText = '<p>Hallo,</p><p>Der Club ' . $company->name . ' hat dich eingeladen dich als Mitglied zu registrieren. Klicke auf den folgenden Link, um dem Club einen Mitglieder-Antrag zu stellen: <br>
        <a href="' . $link . '" target="_blank">' . $link . '</a>.</p>
        <p>GreenclubMaster.de - Das Club Management Tool</p>';

        Mail::html($messageText, function ($message) use ($userEmail, $subject) {
            $message->to($userEmail)
                ->subject($subject)
                ->from('no-reply@optly.de', "Optly.de");
        });
    }

    public function sendNewEmployeeMail($emails, $name)
    {
        $subject = 'Neue Mitarbeiterregistrierung';
        $messageText = '<p>Hallo,</p><p>Ein neuer Mitarbeiter mit dem Namen ' . $name . ' hat sich auf Optly gerade für dein Unternehmen registriert.<br>
        Bitte logge dich bei <a href="https://optly.de" target="_blank">optly.de</a> ein. Unter "Mitarbeiter" findest du alle Mitarbeiter und kannst die fehlenden Informationen für die Mitarbeiter ergänzen.</p>
        <p>Optly.de - Die Gastro Online-Platform</p>';

        Mail::html($messageText, function ($message) use ($emails, $subject) {
            $message->to($emails)
                ->subject($subject)
                ->from('no-reply@optly.de', "Optly.de");
        });
    }

    public function sendNewDocMail($emails, $name,$filename){
        $subject = 'Neues Dokument hochgeladen';
        $messageText = '<p>Hallo,</p><p>Der Mitarbeiter <b>' . $name . '</b> hat soeben das Dokument <b>"' . $filename . '"</b> auf Optly hochgeladen.<br>
        Bitte logge dich bei <a href="https://optly.de" target="_blank">optly.de</a> ein, um das Dokument herunterzuladen.</p>
        <p>Optly.de - Die Gastro Online-Platform</p>';

        Mail::html($messageText, function ($message) use ($emails, $subject) {
            $message->to($emails)
                ->subject($subject)
                ->from('no-reply@optly.de', "Optly.de");
        });
    }

    public function sendMailWithAttachment($email, $filename, $fileContent)
    {
        $userEmail = $email; //'milad1984@gmail.com';
        $subject = 'Neuer Mitarbeiter';
        $messageText = '<p>Hallo,</p><p>Auf Optly wurd ein neuer Mitarbeiter angelegt<br>
        Bitte logge dich bei <a href="https://optly.de" target="_blank">optly.de</a> ein. Unter "Mein Profil" findest du deinen Arbeitsvertrag, bitte unterschreibe diesen.</p>
        <p>Optly.de - Die Gastro Online-Platform</p>';

        Mail::html($messageText, function ($message) use ($userEmail, $subject, $fileContent, $filename) {
            $message->to($userEmail)
                ->subject($subject)
                ->from('no-reply@optly.de', "Optly.de")
                ->attachData($fileContent, $filename, ['mime' => 'application/pdf']);
            //->bcc('bcc1@example.com, bcc2@example.com, bcc3@example.com'); 

        });
    }
}
