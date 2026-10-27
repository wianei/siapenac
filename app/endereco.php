<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    protected $table = "endereco";
    protected $guarded = array('id');

}
