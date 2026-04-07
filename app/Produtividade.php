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

    public function getEficienciaAttribute()
    {
        if ($this->quantidade_defeitos == 0) {
            return 100;
        }

        $eficiencia = (($this->quantidade_produzida - $this->quantidade_defeitos) / $this->quantidade_produzida) * 100;
        return round($eficiencia, 2);
    }
}
