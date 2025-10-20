<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Corriger le livre avec user_id = 0
        echo "Correction du livre avec user_id invalide...\n";
        
        $booksToFix = DB::table('books')
            ->where('user_id', 0)
            ->orWhereNotIn('user_id', function($query) {
                $query->select('id')->from('users');
            })
            ->get();

        if ($booksToFix->isNotEmpty()) {
            echo "Correction de " . $booksToFix->count() . " livres...\n";
            
            // Utiliser le premier utilisateur admin (ID: 1) comme valeur par défaut
            $defaultUserId = 1;
            
            foreach ($booksToFix as $book) {
                echo "Livre ID {$book->id} ('{$book->titre}') : user_id {$book->user_id} -> {$defaultUserId}\n";
            }
            
            DB::table('books')
                ->where('user_id', 0)
                ->orWhereNotIn('user_id', function($query) {
                    $query->select('id')->from('users');
                })
                ->update(['user_id' => $defaultUserId]);
                
            echo "Correction terminée avec succès!\n";
        } else {
            echo "Aucun user_id invalide trouvé.\n";
        }
    }

    public function down()
    {
        // Ne peut pas être annulée car c'est une correction de données
        echo "Cette migration corrige des données et ne peut pas être annulée.\n";
    }
};