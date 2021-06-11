<?php

namespace App\Model;

class Revista extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'revista';
    protected $primaryKey = 'idRevista';
    public $timestamps = false;

    public static function getRevistasManga($idManga)
    {
        return Revista::where('manga_revista.idManga', $idManga)->join('manga_revista', 'revista.idRevista', '=', 'manga_revista.idRevista')->pluck('revista.nombre')->toArray();
    }

    public static function getRevistasEditorialManga($idManga)
    {
        return Revista::where('manga_revista.idManga', $idManga)
            ->join('manga_revista', 'revista.idRevista', '=', 'manga_revista.idRevista')
            ->join('editorial', 'revista.idEditorial', '=', 'editorial.idEditorial')
            ->pluck('CONCAT(revista.nombre, " - ", editorial.nombre)')
            ->toArray();
    }
}
