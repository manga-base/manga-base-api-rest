<?php

namespace App\Model;

use App\Lib\Respuesta;
use Exception;

class Message extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'message';

    public static function saveMail($senderEmail, $senderName, $recipientEmail, $recipientName, $subject, $data)
    {
        try {
            $message = new Message();
            $message->senderEmail = $senderEmail;
            $message->senderName = $senderName;
            $message->recipientEmail = $recipientEmail;
            $message->recipientName = $recipientName;
            $message->subject = $subject;
            $message->status = $data['Messages'][0]['Status'];
            $message->messageUUID = $data['Messages'][0]['To'][0]['MessageUUID'];
            $message->messageID = $data['Messages'][0]['To'][0]['MessageID'];;
            $message->messageHref = $data['Messages'][0]['To'][0]['MessageHref'];;
            $message->save();
            return Respuesta::set(true, '', $message);
        } catch (Exception $error) {
            return Respuesta::set(false, $error);
        }
    }

    public static function checkSpace()
    {
        $messagesCount = Message::whereRaw("DATE(`created_at`) = CURRENT_DATE")->count();
        $messagesCount = $messagesCount ? 0 : $messagesCount;
        return $messagesCount;
    }
}
