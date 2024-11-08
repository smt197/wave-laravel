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
            $table->id();
            $table->string('surname')->unique();
            $table->string('telephone', 15)->unique();
            $table->text('adresse')->nullable();
            $table->text('qr_code')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('solde', 10, 2)->default(0); 
            $table->decimal('soldeMax', 10, 2)->default(50000); 
            $table->decimal('cumulTransaction', 10, 2)->default(100000);   
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
