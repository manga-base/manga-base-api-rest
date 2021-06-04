<?php

namespace App\Lib;

use App\Model\Message;
use \Mailjet\Resources;


class Mail
{
    public static function sendMail($senderEmail, $senderName, $recipientEmail, $recipientName, $subject, $HTMLPart)
    {
        $username = "2bbd5c9b69f334863a44a68493ceac52";
        $password = "60c985b495768fdbc0b6f538f2911c49";
        $mj = new \Mailjet\Client($username, $password, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "$senderEmail",
                        'Name' => "$senderName"
                    ],
                    'To' => [
                        [
                            'Email' => "$recipientEmail",
                            'Name' => "$recipientName"
                        ]
                    ],
                    'Subject' => "$subject",
                    'HTMLPart' => "$HTMLPart"
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if ($response->success()) {
            $data = $response->getData();
            Message::saveMail($senderEmail, $senderName, $recipientEmail, $recipientName, $subject, $data);
            return $data;
        } else {
            return false;
        }
    }

    public static function sendTestMail($to)
    {
        return self::sendMail('no-reply@mangabase.tk', 'No reply', $to, 'You', 'Test mail 2.', '<div style="background-color:#303030; color:#fff; width: 100%; padding: 10px; "><h2>This is a test mail.</h2></div>');
    }

    public static function sendEmailVerification($recipientEmail, $recipientName, $activationCode)
    {
        $url = "https://rest.mangabase.tk/activate/$activationCode";
        $senderEmail = "no-reply@mangabase.tk";
        $senderName = "No reply";
        $subject = "Manga Base - Confirmación del correo electrónico.";
        $HTMLPart = "
            <html>
                <head>
                  <style>
                    .root {
                      width: 100%;
                      background-color: #303030;
                      color: #fff !important;
                      padding: 0;
                      margin: 0;
                      font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Oxygen, Ubuntu, Cantarell, \"Open Sans\", \"Helvetica Neue\", sans-serif;
                    }
                    .header {
                      width: 100%;
                      background-color: #424242;
                    }
                    .logo {
                      width: 40px;
                      height: 40px;
                      padding: 12px;
                      margin-left: 10vw;
                    }
                    .main {
                      width: 35vw;
                      min-width: 300px;
                      margin: auto;
                      font-size: 1.5rem;
                      padding: 0 10vw;
                      font-size: 1rem;
                    }
                    .paper {
                      margin: 24px 0;
                      padding: 24px;
                      background-color: #424242;
                      border-radius: 8px;
                    }
                    .green-text {
                      color: #8fb339;
                    }
                    .button {
                      border: 0;
                      cursor: pointer;
                      margin: 30px auto;
                      display: block;
                      outline: 0;
                      width: 30vw;
                      text-decoration: none;
                      padding: 6px 16px;
                      font-size: 1rem;
                      text-align: center;
                      min-width: 64px;
                      box-sizing: border-box;
                      font-family: Noto Sans JP, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\";
                      font-weight: 700;
                      line-height: 1.75;
                      border-radius: 4px;
                      text-transform: none;
                      color: #000 !important;
                      background-color: #8fb339;
                    }
                    .button:hover {
                      background-color: rgb(100, 125, 39);
                      text-decoration: none;
                    }
                    .button:active {
                      background-color: rgba(101, 125, 39, 0.87);
                    }
                    .link {
                      color: inherit !important;
                      word-break: break-all;
                      text-decoration: none;
                    }
                    .link:hover {
                      text-decoration: underline;
                    }
                    .footer {
                      width: 100%;
                      height: 64px;
                      font-size: 0.75rem;
                      margin-top: 48px;
                    }
                    .footer-text {
                      width: 200px;
                      display: block;
                      margin: auto;
                    }
                  </style>
                </head>
                <body class=\"root\">
                  <div class=\"header\">
                    <a href=\"https://mangabase.tk\" target=\"_blank\" rel=\"noopener noreferrer\">
                      <img class=\"logo\" src=\"https://rest.mangabase.tk/logo.png\" />
                    </a>
                  </div>
                  <div class=\"main\">
                    <div class=\"paper\">
                      <h1>¡Hola, <span class=\"green-text\">$recipientName</span>!</h1>
                      <p style=\"color: #fff;\">Gracias por registrarte en nuestra página, para completar la verificación del email pulsa en este botón de abajo.</p>
                      <a href=\"$url\" target=\"_blank\" rel=\"noopener noreferrer\" class=\"button\"> Verifica tu correo electrónico </a>
                      <p>O verifique usando este enlace: <a class=\"link\" href=\"$url\" target=\"_blank\" rel=\"noopener noreferrer\">$url</a></p>
                      <br />
                      <p style=\"color: #fff;\">Si no has creado ninguna cuenta utilizando este correo electrónico, por favor ignora este email.</p>
                    </div>
                  </div>
                  <div class=\"footer\">
                    <p style=\"color: #fff;\" class=\"footer-text\">
                      ©2021 Manga Base. ·
                      <a class=\"link\" href=\"https://mangabase.tk/contact\" target=\"_blank\" rel=\"noopener noreferrer\">Contacto</a>
                    </p>
                  </div>
                </body>
            </html>
        ";

        return self::sendMail($senderEmail, $senderName, $recipientEmail, $recipientName, $subject, $HTMLPart);
    }
}
