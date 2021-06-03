<?php

namespace App\Lib;

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
            return $response->getData();
        } else {
            return false;
        }
    }

    public static function sendTestMail($to)
    {
        return self::sendMail('no-reply@mangabase.tk', 'No reply', $to, 'You', 'Test mail 2.', '<div style="backgrownd-color:#C3C3C3; color:#fff; width: 100%; "><h2>This is a test mail.</h2></div>');
    }
}
