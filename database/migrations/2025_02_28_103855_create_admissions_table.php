<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->string('numero_patient');
            $table->string('lit');
            $table->dateTime('date_arrivee');
            $table->dateTime('date_depart')->nullable();
            $table->integer('duree_attente')->nullable();
            $table->integer('taux_occupation')->nullable();
            $table->string('maladie');
            $table->boolean('soins_intensif')->default(false);
            $table->string('medicaments')->nullable();
            $table->string('type_admissions');
            $table->string('service_medical');
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
        Schema::dropIfExists('admissions');
    }
}