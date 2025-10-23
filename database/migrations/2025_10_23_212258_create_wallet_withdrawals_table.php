<?php

declare(strict_types=1);

use App\Models\Wallet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->mediumInteger('amount');
            $table->string('status');
            $table->foreignIdFor(Wallet::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_withdrawal_requests');
    }
};
