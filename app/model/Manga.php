<?php

namespace App\Model;

class Manga extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'manga';

    public static function getColumns()
    {
        return ['id', 'tituloPreferido', 'tituloJA', 'tituloRōmaji', 'tituloES', 'tituloEN', 'foto', 'argumento', 'añoDePublicacion', 'añoDeFinalizacion', 'capitulos', 'volumenes', 'nota', 'idEstado', 'idDemografia'];
    }

    public static function getSmallColumns()
    {
        return ['id', 'tituloPreferido', 'foto', 'añoDePublicacion', 'nota', 'idEstado', 'idDemografia'];
    }
}
