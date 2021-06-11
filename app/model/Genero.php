<?php

namespace App\Model;

class Genero extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'genero';
    protected $primaryKey = 'idGenero';
    public $timestamps = false;

    public static function getGenerosManga($idManga)
    {
        return Genero::where('manga_genero.idManga', $idManga)->join('manga_genero', 'genero.idGenero', '=', 'manga_genero.idGenero')->get();
    }

    public static function getGenerosMangaArray($idManga)
    {
        return Genero::where('manga_genero.idManga', $idManga)->join('manga_genero', 'genero.idGenero', '=', 'manga_genero.idGenero')->pluck('genero.genero')->toArray();
    }
}
