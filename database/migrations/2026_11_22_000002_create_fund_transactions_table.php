<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fund_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->date('date');
            $table->string('category', 100);
            $table->string('spending_type', 100)->nullable();
            $table->string('funding_source', 100)->nullable();
            $table->string('description', 500);
            $table->unsignedBigInteger('amount');
            $table->string('vendor', 150)->nullable();
            $table->string('receipt_number', 100)->nullable();
            $table->string('evidence_url', 500)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['village_id', 'date']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fund_transactions');
    }
};
