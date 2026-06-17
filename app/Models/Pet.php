<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = ["user_id", "name", "breed", "age", "special_notes"];
}
