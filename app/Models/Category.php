<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $guarded=[];
    public $timestamps = false;

    public function lots(){
        return $this->hasMany(Lot::class);
    }
}
