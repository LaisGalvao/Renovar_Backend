<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class register extends Model
{
    use HasFactory;
     protected $fillable = [
        'nome',
        'email',
        'CPF',
        'idade',
        'telefone',
        'password',
    ];
}
