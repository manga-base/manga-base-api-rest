<?php

use App\Lib\Respuesta;
use App\Model\Message;

$app->group('/message/', function () {

    $this->get(
        '',
        function ($req, $res, $args) {
            try {
                $data = [
                    "Messages" => [
                        [
                            "Status" => "success",
                            "CustomID" => "",
                            "To" => [
                                [
                                    "Email" => "antoniaisern@hotmail.com",
                                    "MessageUUID" => "46cf39f4-2f07-4a48-a1f5-15580ba831e1",
                                    "MessageID" => 1152921512411644757,
                                    "MessageHref" => "https://api.mailjet.com/v3/REST/message/1152921512411644757"
                                ]
                            ],
                            "Cc" => [],
                            "Bcc" => []
                        ]
                    ]
                ];
                $resposta = Message::saveMail("yo", "yo", "tu", "tu", "Subject", $data);
                return $res->withJson(Respuesta::set(true, '', $resposta));
            } catch (Exception $error) {
                Respuesta::set(false, $error);
                return $res->withJson(Respuesta::toString());
            }
        }
    );
});
