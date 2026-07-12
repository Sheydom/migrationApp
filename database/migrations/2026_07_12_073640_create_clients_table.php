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
        Schema::create('clients', function (Blueprint $table) {
//personal information
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->string('occupation')->nullable();
            $table->string('nationality');
            $table->string('passport_number')->nullable();
            $table->string('country_of_residence')->nullable();
//contact
            $table->string('phone')->nullable();
            $table->string('email')->unique();
//Visa
            $table->string('current_visa')->nullable();
            $table->date('expire_date')->nullable();
//Internal
            $table->string('status')->default('new');
            $table->text('notes')->nullable();
            $table->string('folder_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
