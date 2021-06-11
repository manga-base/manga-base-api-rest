<?php

namespace App\Model;

class Revista extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'revista';
    protected $primaryKey = 'idRevista';
    public $timestamps = false;

    public static function getRevistasManga($idManga)
    {
        return Revista::select('revista.idRevista', 'revista.nombre')->where('manga_revista.idManga', $idManga)->join('manga_revista', 'revista.idRevista', '=', 'manga_revista.idRevista')->get();
    }

    public static function getRevistasMangaArray($idManga)
    {
        return Revista::where('manga_revista.idManga', $idManga)->join('manga_revista', 'revista.idRevista', '=', 'manga_revista.idRevista')->pluck('revista.nombre')->toArray();
    }
}
