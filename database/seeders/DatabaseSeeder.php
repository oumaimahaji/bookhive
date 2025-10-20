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

        // Créer un utilisateur admin s'il n'existe pas
        User::firstOrCreate(
            ['email' => 'admin@bookshare.com'],
            [
                'name' => 'Admin BookShare',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'phone' => '12345678',
            ]
        );

        // Créer un utilisateur normal de test uniquement s'il n'existe pas
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );
    }
}
