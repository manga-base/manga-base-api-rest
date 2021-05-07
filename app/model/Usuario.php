<?php

namespace App\Model;

class Usuario extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'usuario';

    public static function getColumns()
    {
        return ['id', 'username', 'password', 'email', 'birthdayDate', 'avatar', 'banner', 'biografia', 'created_at'];
    }
}
