<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produtividade extends Model
{
    protected $fillable = [
        'linha_produto',
        'quantidade_produzida',
        'quantidade_defeitos',
        'data_producao',
    ];

    protected $casts = [
        'data_producao' => 'date',
    ];
}
