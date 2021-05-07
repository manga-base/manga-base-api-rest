<?php
$app->get(
    '/whoami',
    function ($req, $res, $args) {
        $decodetToken = $req->getAttribute('decoded_token_data');
        return $res->withJson($decodetToken["usuario"]);
    }
);
