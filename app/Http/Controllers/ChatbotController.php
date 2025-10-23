<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Book;
use App\Models\Category;

class ChatbotController extends Controller
{
    public function askChatbot(Request $request)
    {
        try {
            // Handle both JSON and form data - get message from various sources
            $message = $request->input('message') ?:
                      $request->get('message') ?:
                      $request->json('message') ?:
                      '';

            // Trim and validate the message
            $message = trim($message);

            if (empty($message)) {
                return response()->json([
                    'response' => 'Veuillez saisir un message avant d\'envoyer.'
                ], 422);
            }

            if (strlen($message) > 500) {
                return response()->json([
                    'response' => 'Le message ne peut pas dépasser 500 caractères.'
                ], 422);
            }

            // Process the message and get response from database
            return $this->processMessage($message);

        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            return response()->json([
                'response' => 'Désolé, une erreur inattendue s\'est produite. Veuillez réessayer.'
            ], 500);
        }
    }

    private function processMessage($message)
    {
        $messageLower = strtolower($message);

        // Handle book availability queries
        if (strpos($messageLower, 'livre') !== false || strpos($messageLower, 'disponible') !== false || strpos($messageLower, 'available') !== false) {
            return $this->getAvailableBooksResponse();
        }

        // Handle reservation queries
        if (strpos($messageLower, 'réservation') !== false || strpos($messageLower, 'réserver') !== false || strpos($messageLower, 'reserve') !== false) {
            return response()->json([
                'response' => 'Pour réserver un livre, connectez-vous et allez dans la section "Réservations". Vous pourrez y voir tous les livres disponibles et faire votre demande.'
            ]);
        }

        // Handle due date queries
        if (strpos($messageLower, 'date') !== false || strpos($messageLower, 'retour') !== false || strpos($messageLower, 'due') !== false) {
            return $this->getDueDatesResponse();
        }

        // Handle specific book queries
        if (strpos($messageLower, 'titre') !== false || strpos($messageLower, 'auteur') !== false || strpos($messageLower, 'author') !== false) {
            return $this->getBookInfoResponse($message);
        }

        // Handle category queries
        if (strpos($messageLower, 'catégorie') !== false || strpos($messageLower, 'category') !== false) {
            return $this->getCategoriesResponse();
        }

        // Handle help queries
        if (strpos($messageLower, 'aide') !== false || strpos($messageLower, 'help') !== false || strpos($messageLower, 'comment') !== false) {
            return $this->getHelpResponse();
        }

        // Handle greeting queries
        if (strpos($messageLower, 'bonjour') !== false || strpos($messageLower, 'salut') !== false || strpos($messageLower, 'hello') !== false) {
            return $this->getGreetingResponse();
        }

        // Default response
        return response()->json([
            'response' => 'Je suis votre assistant BookHive ! Je peux vous aider avec les livres disponibles, les réservations, les dates de retour, et les informations sur nos collections. Que voulez-vous savoir ?'
        ]);
    }

    private function getAvailableBooksResponse()
    {
        try {
            // Get available books (books that are valid and have PDF or are available for reservation)
            $availableBooks = Book::where('is_valid', true)
                ->with('category')
                ->limit(10)
                ->get();

            if ($availableBooks->count() > 0) {
                $bookList = $availableBooks->map(function($book) {
                    $category = $book->category ? $book->category->nom : 'Non catégorisé';
                    return " • {$book->titre} de {$book->auteur} ({$category})";
                })->take(5)->implode("\n");

                $response = "📚 Voici nos livres disponibles :\n\n{$bookList}";

                if ($availableBooks->count() > 5) {
                    $response .= "\n\nEt " . ($availableBooks->count() - 5) . " autres livres ! Consultez la section 'Livres' pour voir toute notre collection.";
                }

                return response()->json(['response' => $response]);
            } else {
                return response()->json([
                    'response' => 'Actuellement, nous mettons à jour notre collection. Revenez bientôt pour découvrir de nouveaux livres !'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error getting available books: ' . $e->getMessage());
            return response()->json([
                'response' => 'Je peux vous aider avec les livres ! Consultez la section "Livres" pour voir notre collection disponible.'
            ]);
        }
    }

    private function getDueDatesResponse()
    {
        try {
            // This would typically query reservations, but for now provide general info
            return response()->json([
                'response' => '📅 Les dates de retour dépendent de chaque réservation. Connectez-vous et allez dans "Mes Réservations" pour voir vos dates limites de retour. Généralement, vous avez 14 jours pour lire vos livres réservés.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting due dates: ' . $e->getMessage());
            return response()->json([
                'response' => 'Pour voir vos dates de retour, consultez la section "Réservations" après vous être connecté.'
            ]);
        }
    }

    private function getBookInfoResponse($message)
    {
        try {
            $messageLower = strtolower($message);

            // Try to find books by title or author
            $books = Book::where('is_valid', true)
                ->where(function($query) use ($messageLower) {
                    $query->whereRaw('LOWER(titre) LIKE ?', ['%' . $messageLower . '%'])
                          ->orWhereRaw('LOWER(auteur) LIKE ?', ['%' . $messageLower . '%']);
                })
                ->with('category')
                ->limit(3)
                ->get();

            if ($books->count() > 0) {
                $bookList = $books->map(function($book) {
                    $category = $book->category ? $book->category->nom : 'Non catégorisé';
                    return " • {$book->titre} de {$book->auteur} ({$category})";
                })->implode("\n");

                return response()->json([
                    'response' => "🔍 J'ai trouvé ces livres correspondants à votre recherche :\n\n{$bookList}"
                ]);
            } else {
                return response()->json([
                    'response' => 'Je n\'ai pas trouvé de livres correspondant à votre recherche. Essayez d\'autres mots-clés ou consultez notre collection complète dans la section "Livres".'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error getting book info: ' . $e->getMessage());
            return response()->json([
                'response' => 'Pour chercher des livres spécifiques, utilisez la barre de recherche dans la section "Livres".'
            ]);
        }
    }

    private function getCategoriesResponse()
    {
        try {
            $categories = Category::all();

            if ($categories->count() > 0) {
                $categoryList = $categories->map(function($category) {
                    return " • {$category->nom}";
                })->implode("\n");

                return response()->json([
                    'response' => "📂 Voici nos catégories de livres :\n\n{$categoryList}\n\nVous pouvez filtrer par catégorie dans la section 'Livres'."
                ]);
            } else {
                return response()->json([
                    'response' => 'Découvrez tous nos livres dans la section "Livres" de notre bibliothèque !'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error getting categories: ' . $e->getMessage());
            return response()->json([
                'response' => 'Parcourez nos collections dans la section "Livres" pour découvrir tous nos ouvrages disponibles.'
            ]);
        }
    }

    private function getHelpResponse()
    {
        return response()->json([
            'response' => "ℹ️ Je peux vous aider avec :\n\n• 📚 Livres disponibles et recherche\n• ℹ️ Informations sur nos collections\n• 📅 Réservations et dates de retour\n• 📂 Catégories et organisation\n\nQue voulez-vous savoir sur BookHive ?"
        ]);
    }

    private function getGreetingResponse()
    {
        return response()->json([
            'response' => '👋 Bonjour ! Bienvenue sur BookHive, votre bibliothèque en ligne. Je suis là pour vous aider à découvrir nos livres, faire des réservations, et répondre à toutes vos questions. Comment puis-je vous assister aujourd\'hui ?'
        ]);
    }
}