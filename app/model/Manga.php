<?php

namespace App\Model;

class Manga extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'manga';

    public static function getSmallColumns()
    {
        return ['id', 'tituloPreferido', 'foto', 'añoDePublicacion', 'nota', 'idEstado', 'idDemografia'];
    }
}
