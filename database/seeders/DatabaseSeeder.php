<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Supprimer l'admin s'il existe déjà
        User::where('email', 'admin@bookshare.com')->delete();

        // Créer un utilisateur admin
        User::create([
            'name' => 'Admin BookShare',
            'email' => 'admin@bookshare.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'phone' => '12345678',
        ]);

        // Optionnel : créer un utilisateur normal de test
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);
    }
}
