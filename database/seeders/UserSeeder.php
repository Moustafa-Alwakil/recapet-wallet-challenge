<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()
            ->create([
                'name' => 'Moustafa Alwakil',
                'email' => 'moustafaalwakil@gmail.com',
                'password' => 'Password@123',
            ])
            ->wallet
            ->increment('balance_in_cents', 1000000);

        User::factory(100)
            ->create()
            ->each(function (User $user) {
                $user->wallet->increment('balance_in_cents', 1000000);
            });
    }
}
