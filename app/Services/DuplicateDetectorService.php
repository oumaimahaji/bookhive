<?php

namespace App\Services;

use App\Models\Book;

class DuplicateDetectorService
{
    /**
     * Calcule la similarité entre deux chaînes avec plusieurs méthodes
     */
    public function textSimilarity($string1, $string2)
    {
        if (empty($string1) || empty($string2)) {
            return 0;
        }

        $str1 = mb_strtolower(trim($string1));
        $str2 = mb_strtolower(trim($string2));

        // 1. Si identiques
        if ($str1 === $str2) {
            return 100;
        }

        // 2. Vérifier si l'un contient l'autre (important !)
        if (str_contains($str1, $str2) || str_contains($str2, $str1)) {
            $containScore = $this->calculateContainmentScore($str1, $str2);
            if ($containScore >= 70) {
                return $containScore;
            }
        }

        // 3. Similarité des mots clés
        $keywordScore = $this->keywordSimilarity($str1, $str2);
        if ($keywordScore >= 80) {
            return $keywordScore;
        }

        // 4. Distance de Levenshtein (backup)
        $levenshtein = levenshtein($str1, $str2);
        $maxLen = max(mb_strlen($str1), mb_strlen($str2));

        if ($maxLen === 0) return 0;

        $levenshteinScore = max(0, 100 - ($levenshtein / $maxLen * 100));

        // Prendre le meilleur score
        return max($keywordScore, $levenshteinScore, $containScore ?? 0);
    }

    /**
     * Calcule le score quand un texte contient l'autre
     */
    private function calculateContainmentScore($str1, $str2)
    {
        $len1 = mb_strlen($str1);
        $len2 = mb_strlen($str2);

        if ($len1 === 0 || $len2 === 0) return 0;

        $containment = min($len1, $len2) / max($len1, $len2) * 100;

        // Bonus si c'est le début du titre
        if (str_starts_with($str1, $str2) || str_starts_with($str2, $str1)) {
            $containment += 20;
        }

        return min(100, $containment);
    }

    /**
     * Similarité basée sur les mots clés
     */
    private function keywordSimilarity($str1, $str2)
    {
        $words1 = $this->extractKeywords($str1);
        $words2 = $this->extractKeywords($str2);

        if (empty($words1) || empty($words2)) {
            return 0;
        }

        $commonWords = array_intersect($words1, $words2);
        $totalWords = count(array_unique(array_merge($words1, $words2)));

        if ($totalWords === 0) return 0;

        $similarity = (count($commonWords) / $totalWords) * 100;

        // Bonus si les premiers mots correspondent
        if (!empty($words1[0]) && !empty($words2[0]) && $words1[0] === $words2[0]) {
            $similarity += 15;
        }

        return min(100, $similarity);
    }

    /**
     * Extrait les mots clés importants
     */
    private function extractKeywords($text)
    {
        // Supprimer la ponctuation
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);

        // Mots à ignorer
        $stopWords = ['le', 'la', 'les', 'un', 'une', 'des', 'du', 'de', 'et', 'ou', 'où', 'à', 'a', 'dans', 'en', 'sur', 'avec', 'pour', 'par', 'd', 'l', 'the', 'and', 'of', 'in', 'on', 'at', 'to', 'for'];

        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $words = array_map('trim', $words);
        $words = array_diff($words, $stopWords);
        $words = array_filter($words, function ($word) {
            return mb_strlen($word) > 2; // Garder seulement les mots de plus de 2 caractères
        });

        return array_values($words);
    }

    /**
     * Détecte les doublons potentiels pour un nouveau livre
     */
    public function findPotentialDuplicates($titre, $auteur, $excludeId = null)
    {
        $potentialDuplicates = [];

        if (empty($titre)) {
            return $potentialDuplicates;
        }

        // Chercher dans tous les livres existants
        $query = Book::with('category');
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $existingBooks = $query->get();

        foreach ($existingBooks as $book) {
            $score = $this->calculateDuplicateScore($titre, $auteur, $book);

            if ($score >= 50) { // Seuil plus bas pour mieux détecter
                $potentialDuplicates[] = [
                    'book' => $book,
                    'score' => $score,
                    'reasons' => $this->getDuplicateReasons($titre, $auteur, $book)
                ];
            }
        }

        // Trier par score décroissant
        usort($potentialDuplicates, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $potentialDuplicates;
    }

    /**
     * Calcule un score global de similarité (0-100)
     */
    private function calculateDuplicateScore($newTitre, $newAuteur, $existingBook)
    {
        $titreSimilarity = $this->textSimilarity($newTitre, $existingBook->titre);

        $auteurSimilarity = $this->textSimilarity($newAuteur, $existingBook->auteur);

        // Pondération : le titre est beaucoup plus important
        $score = ($titreSimilarity * 0.8) + ($auteurSimilarity * 0.2);

        return min(100, round($score, 2));
    }

    /**
     * Génère les raisons de la détection
     */
    private function getDuplicateReasons($newTitre, $newAuteur, $existingBook)
    {
        $reasons = [];

        $titreSimilarity = $this->textSimilarity($newTitre, $existingBook->titre);
        $auteurSimilarity = $this->textSimilarity($newAuteur, $existingBook->auteur);

        if ($titreSimilarity >= 90) {
            $reasons[] = "Titre presque identique";
        } elseif ($titreSimilarity >= 70) {
            $reasons[] = "Titre très similaire";
        } elseif ($titreSimilarity >= 50) {
            $reasons[] = "Titre partiellement similaire";
        }

        if ($auteurSimilarity >= 95) {
            $reasons[] = "Même auteur";
        } elseif ($auteurSimilarity >= 80) {
            $reasons[] = "Auteur similaire";
        }

        // Détection spéciale pour les séries
        if ($this->isSameSeries($newTitre, $existingBook->titre)) {
            $reasons[] = "Même série détectée";
        }

        return $reasons;
    }

    /**
     * Détecte si les titres font partie de la même série
     */
    private function isSameSeries($titre1, $titre2)
    {
        $t1 = mb_strtolower($titre1);
        $t2 = mb_strtolower($titre2);

        // Patterns de séries communes
        $seriesPatterns = [
            '/harry potter.*(\d+|pierre|chambre|prisonnier|coupe|ordre|prince|reliques)/',
            '/seigneur.*anneaux/',
            '/hunger games/',
            '/games.*thrones/',
            '/chroniques.*narnia/',
            '/twilight/'
        ];

        foreach ($seriesPatterns as $pattern) {
            if (preg_match($pattern, $t1) && preg_match($pattern, $t2)) {
                return true;
            }
        }

        return false;
    }
}
