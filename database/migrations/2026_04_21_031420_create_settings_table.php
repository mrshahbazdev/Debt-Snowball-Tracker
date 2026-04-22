<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('monthly_revenue', 15, 2)->default(0);
            $table->decimal('debt_allocation_percent', 6, 3)->default(1.000); // percent, e.g. 1.000 = 1%
            $table->decimal('minimum_cash_buffer', 15, 2)->default(0);
            $table->boolean('new_debt_allowed')->default(false);
            $table->string('currency', 8)->default('EUR');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
