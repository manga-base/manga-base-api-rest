<?php

use App\Lib\Mail;

$app->get(
    '/mail',
    function ($req, $res, $args) {
        return $res->withJson(Mail::sendTestMail('bartomeupauclar@paucasesnovescifp.cat'));
    }
);
