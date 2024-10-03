<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unidades', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('unidade', length: 5);
            $table->string('descricao', length: 30);
        });

        // adicionando relacionamento com a tabela produtos
        Schema::table('produtos', function (Blueprint $table){
            $table->unsignedBigInteger('unidade_id');
            $table->foreign('unidade_id')->references('id')->on('unidades');
        });

        // adicionando relacionamento com a tabela produto_detalhes
        Schema::table('produto_detalhes', function (Blueprint $table){
            $table->unsignedBigInteger('unidade_id');
            $table->foreign('unidade_id')->references('id')->on('unidades');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // adicionando relacionamento com a tabela produto_detalhes
        Schema::table('produto_detalhes', function (Blueprint $table){
            $table->dropForeign('produto_detalhes_unidade_id_foreign');
            $table->dropColumn('unidade_id');
        });

        // adicionando relacionamento com a tabela produtos
        Schema::table('produtos', function (Blueprint $table){
            $table->dropForeign('produtos_unidade_id_foreign');
            $table->dropColumn('unidade_id');
        });

        Schema::dropIfExists('unidades');
    }
};
