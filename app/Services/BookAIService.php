<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class BookAIService
{
    public function getAIRecommendations($title, $author = "")
    {
        try {
            // URL de votre service IA sur le port 5000
            $url = 'http://127.0.0.1:5000/analyze-book';

            // Données à envoyer
            $data = [
                'title' => $title,
                'author' => $author
            ];

            // Utiliser cURL pour plus de fiabilité
            $ch = curl_init();
            
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen(json_encode($data))
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_error($ch)) {
                throw new \Exception('Erreur cURL: ' . curl_error($ch));
            }
            
            curl_close($ch);

            Log::info('Réponse service IA HTTP:', [
                'http_code' => $httpCode,
                'response' => $response
            ]);

            // Parser la réponse JSON
            $result = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Réponse JSON invalide: ' . $response);
            }

            // CORRECTION : Vérifier la structure de la réponse
            if (isset($result['success']) && $result['success'] === true) {
                $responseData = [
                    'success' => true,
                    'generated_description' => $result['generated_description'] ?? "Découvrez '{$title}' de {$author}, un livre captivant qui emmènera les lecteurs dans un voyage littéraire inoubliable."
                ];

                // Ajouter la catégorie seulement si elle existe
                if (isset($result['recommended_category']) && !empty($result['recommended_category'])) {
                    $responseData['recommended_category'] = $result['recommended_category'];
                }

                return $responseData;
            } else {
                throw new \Exception($result['error'] ?? 'Erreur inconnue du service IA');
            }

        } catch (\Exception $e) {
            Log::error('Erreur service IA:', [
                'error' => $e->getMessage(),
                'title' => $title,
                'author' => $author
            ]);

            // Solution de repli sans catégorie forcée
            return [
                'success' => true,
                'generated_description' => "Découvrez '{$title}' de {$author}, un livre captivant qui emmènera les lecteurs dans un voyage littéraire inoubliable."
                // Pas de catégorie par défaut
            ];
        }
    }
}