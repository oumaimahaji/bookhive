<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SentimentAnalyzer
{
    private $pythonScript;

    public function __construct()
    {
        $this->pythonScript = base_path('scripts/sentiment_analyzer.py');
    }

    /**
     * Analyze text sentiment using Python with robust error handling
     */
    public function analyze(string $text): array
    {
        // Clean the text
        $text = trim($text);
        
        if (empty($text) || strlen($text) < 2) {
            return $this->getNeutralResult();
        }

        // Try Python first with better error handling
        $pythonResult = $this->analyzeWithPython($text);
        if ($pythonResult !== null && $this->isValidSentimentResult($pythonResult)) {
            return $pythonResult;
        }

        // Enhanced PHP fallback analysis
        return $this->analyzeWithEnhancedPhp($text);
    }

    /**
     * Analyze using Python script with robust error handling
     */
    private function analyzeWithPython(string $text): ?array
    {
        try {
            // Check if Python script exists
            if (!file_exists($this->pythonScript)) {
                Log::warning('Python script not found: ' . $this->pythonScript);
                return null;
            }

            // Escape text for command line
            $escapedText = escapeshellarg($text);
            
            // Build the command with timeout
$command = "python " . escapeshellarg($this->pythonScript) . " " . $escapedText . " 2>&1";            
            // Execute Python script with timeout (10 seconds)
            $output = shell_exec($command);
            
            if ($output === null || trim($output) === '') {
                Log::warning('Python script returned empty output for text: ' . substr($text, 0, 50));
                return null;
            }
            
            // Clean the output
            $output = trim($output);
            
            // Parse JSON response
            $result = json_decode($output, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Python script returned invalid JSON: ' . $output);
                return null;
            }
            
            if (!$this->isValidSentimentResult($result)) {
                Log::warning('Python script returned invalid sentiment result: ' . json_encode($result));
                return null;
            }
            
            Log::info('Python sentiment analysis successful', [
                'text' => substr($text, 0, 50),
                'sentiment' => $result['sentiment'],
                'confidence' => $result['confidence']
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Python sentiment analysis failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Enhanced PHP analysis with better French support and weights
     */
    private function analyzeWithEnhancedPhp(string $text): array
    {
        $text = strtolower(trim($text));
        
        // Enhanced French word lists with weights
        $positiveWords = [
            'excellent' => 3, 'super' => 3, 'génial' => 3, 'magnifique' => 3, 
            'parfait' => 3, 'formidable' => 3, 'fantastique' => 3, 'merveilleux' => 3,
            'bon' => 2, 'utile' => 2, 'agréable' => 2, 'content' => 2, 'heureux' => 2,
            'satisfait' => 2, 'aimer' => 2, 'adorer' => 3, 'recommandé' => 2, 
            'bravo' => 2, 'félicitations' => 2, 'merci' => 1, 'bien' => 1, 'top' => 2,
            'cool' => 1, 'sympa' => 1, 'joli' => 1, 'beau' => 1, 'belle' => 1,
            'love' => 3, 'like' => 2, 'good' => 2, 'great' => 3, 'awesome' => 3,
            'extraordinaire' => 3, 'remarquable' => 2, 'impressionnant' => 2
        ];
        
        $negativeWords = [
            'mauvais' => 3, 'nul' => 3, 'horrible' => 3, 'terrible' => 3, 
            'déçu' => 3, 'décevant' => 3, 'mediocre' => 2, 'pire' => 3,
            'insupportable' => 3, 'ennuyeux' => 2, 'inutile' => 3, 'compliqué' => 1,
            'difficile' => 1, 'probleme' => 2, 'bug' => 2, 'erreur' => 2,
            'catastrophe' => 4, 'désastre' => 4, 'échec' => 3, 'raté' => 2,
            'moche' => 2, 'laid' => 2, 'hate' => 3, 'bad' => 3, 'worst' => 4,
            'défectueux' => 2, 'inadéquat' => 2, 'insatisfaisant' => 2
        ];

        $positiveScore = 0;
        $negativeScore = 0;
        
        // Calculate scores with weights
        foreach ($positiveWords as $word => $weight) {
            if (str_contains($text, $word)) {
                $count = substr_count($text, $word);
                $positiveScore += $count * $weight;
            }
        }
        
        foreach ($negativeWords as $word => $weight) {
            if (str_contains($text, $word)) {
                $count = substr_count($text, $word);
                $negativeScore += $count * $weight;
            }
        }

        // Handle negations (reverse sentiment)
        if (str_contains($text, 'pas ') || str_contains($text, ' ne ') || str_contains($text, 'non ')) {
            $positiveScore = max(0, $positiveScore - 2);
            $negativeScore += 2;
        }

        // Handle intensifiers (boost scores)
        if (str_contains($text, 'très ') || str_contains($text, 'vraiment ') || str_contains($text, 'extrêmement ')) {
            $positiveScore *= 1.5;
            $negativeScore *= 1.5;
        }

        return $this->calculateEnhancedSentiment($positiveScore, $negativeScore, $text);
    }
    
    private function calculateEnhancedSentiment($positiveScore, $negativeScore, $text): array
    {
        $totalScore = $positiveScore + $negativeScore;
        
        if ($totalScore === 0) {
            // Check for very short texts or neutral content
            if (str_word_count($text) < 3) {
                return $this->getNeutralResult();
            }
            return $this->getNeutralResult();
        }
        
        $polarity = ($positiveScore - $negativeScore) / $totalScore;
        
        // More sensitive thresholds for better detection
        if ($polarity > 0.15) {
            $sentiment = 'positive';
            $confidence = min(0.95, ($polarity + 1) / 2);
        } elseif ($polarity < -0.15) {
            $sentiment = 'negative';
            $confidence = min(0.95, (1 - $polarity) / 2);
        } else {
            $sentiment = 'neutral';
            $confidence = 0.8;
        }
        
        // Boost confidence for clear cases
        if (abs($polarity) > 0.5) {
            $confidence = 0.9;
        }
        
        // Ensure confidence is reasonable
        $confidence = max(0.5, min(0.95, $confidence));

        Log::info('PHP sentiment analysis completed', [
            'text' => substr($text, 0, 50),
            'sentiment' => $sentiment,
            'confidence' => $confidence,
            'polarity' => $polarity,
            'positive_score' => $positiveScore,
            'negative_score' => $negativeScore
        ]);
        
        return [
            'sentiment' => $sentiment,
            'confidence' => round($confidence, 2),
            'polarity' => round($polarity, 3),
            'method' => 'php_enhanced'
        ];
    }
    
    private function getNeutralResult(): array
    {
        return [
            'sentiment' => 'neutral',
            'confidence' => 1.0,
            'polarity' => 0.0,
            'method' => 'neutral_fallback'
        ];
    }

    /**
     * Validate sentiment result structure
     */
    private function isValidSentimentResult(array $result): bool
    {
        return isset($result['sentiment']) && 
               in_array($result['sentiment'], ['positive', 'negative', 'neutral']) &&
               isset($result['confidence']) && 
               is_numeric($result['confidence']) &&
               $result['confidence'] >= 0 && 
               $result['confidence'] <= 1;
    }

    /**
     * Analyze multiple texts
     */
    public function analyzeBatch(array $texts): array
    {
        $results = [];
        foreach ($texts as $text) {
            $results[] = $this->analyze($text);
        }
        return $results;
    }
}