<?php

namespace App\Model;

class Autor extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'autor';
    protected $primaryKey = 'idAutor';
    public $timestamps = false;

    public static function getAutoresManga($idManga)
    {
        return Autor::where('manga_autor.idManga', $idManga)->join('manga_autor', 'autor.idAutor', '=', 'manga_autor.idAutor')->get();
    }

    public static function getAutoresMangaArray($idManga)
    {
        return Autor::where('manga_autor.idManga', $idManga)->join('manga_autor', 'autor.idAutor', '=', 'manga_autor.idAutor')->pluck('autor.nombre')->toArray();
    }
}
