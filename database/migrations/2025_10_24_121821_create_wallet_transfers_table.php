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
        Schema::create('wallet_transfers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('amount_in_cents');
            $table->bigInteger('fee_in_cents')->default(0);
            $table->string('status');
            $table->foreignIdFor(Wallet::class, 'sender_wallet_id')->constrained();
            $table->foreignIdFor(Wallet::class, 'receiver_wallet_id')->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transfers');
    }
};
