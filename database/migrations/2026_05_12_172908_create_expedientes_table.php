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
        Schema::create('expedientes', function (Blueprint $table) {
            $table->id('id_registro');
            $table->string('numero_expediente', 50)->unique();
            $table->unsignedBigInteger('id_materia')->nullable();
            $table->unsignedBigInteger('id_juzgado')->nullable();
            $table->text('demandante')->nullable();
            $table->text('demandado')->nullable();
            $table->unsignedBigInteger('id_estado')->nullable();
            $table->date('fecha_resolucion')->nullable();
            $table->text('contenido_resolucion')->nullable();
            $table->text('antecedentes')->nullable();
            $table->timestamp('fecha_carga')->useCurrent();
            $table->foreign('id_materia')->references('id_materia')->on('materias')->nullOnDelete();
            $table->foreign('id_juzgado')->references('id_juzgado')->on('juzgados')->nullOnDelete();
            $table->foreign('id_estado')->references('id_estado')->on('estados')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expedientes');
    }
};
