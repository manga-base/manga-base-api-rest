<?php

namespace App\Model;

class Autor extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'autor';
    protected $primaryKey = 'idAutor';
    public $timestamps = false;
}
