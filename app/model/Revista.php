<?php

namespace App\Model;

class Revista extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'revista';
    protected $primaryKey = 'idRevista';
    public $timestamps = false;

    public static function getRevistasManga($idManga)
    {
        return Revista::where('manga_revista.idManga', $idManga)->join('manga_revista', 'revista.idRevista', '=', 'manga_revista.idRevista')->get();
    }

    public static function getRevistasEditorialManga($idManga)
    {
        return Revista::selectRaw('CONCAT(revista.nombre, " - ", editorial.nombre) AS label')
            ->where('manga_revista.idManga', $idManga)
            ->join('manga_revista', 'revista.idRevista', '=', 'manga_revista.idRevista')
            ->join('editorial', 'revista.idEditorial', '=', 'editorial.idEditorial')
            ->pluck('label')
            ->toArray();
    }
}
