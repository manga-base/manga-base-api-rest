<?php

namespace App\Lib;

#require '../vendor/autoload.php';

use Mailjet\Resources;

class Mail
{
    public static function sendMail($toEmail, $subject, $HTMLPart, $fromEmail, $fromName)
    {
        $mj = new \Mailjet\Client('2bbd5c9b69f334863a44a68493ceac52', '60c985b495768fdbc0b6f538f2911c49', true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $fromEmail,
                        'Name' => $fromName
                    ],
                    'To' => [
                        [
                            'Email' => $toEmail
                        ]
                    ],
                    'Subject' => $subject,
                    'HTMLPart' => $HTMLPart
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        return $response->success();
        //  && var_dump($response->getData());

    }

    public static function sendActivationAccountMail($toEmail, $activationCode)
    {
        $HTMLPart = "<h1>Verificación de correo electrónico</h1>";
        $HTMLPart .= "<p>Para verificar tu correo electrónico haz click en este link:</p>";
        $HTMLPart .= "<p>https://rest.mangabase.tk/usuario/activar/" . urlencode($toEmail) . "/" . $activationCode . "</p>";
        return self::sendMail($toEmail, "Verificación de correo electrónico - Manga Base", $HTMLPart, "no-reply@mangabase.tk", "No contestar a este correo.");
    }
}
