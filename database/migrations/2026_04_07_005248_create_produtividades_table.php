<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdutividadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produtividades', function (Blueprint $table) {
            $table->id();
            $table->string('linha_produto')->index();
            $table->unsignedInteger('quantidade_produzida');
            $table->unsignedInteger('quantidade_defeitos');
            $table->date('data_producao');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produtividades');
    }
}
