<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TwinwordSentimentService
{
    protected $apiKey;
    protected $baseUrl = 'https://twinword-sentiment-analysis.p.rapidapi.com/analyze/';

    public function __construct()
    {
        $this->apiKey = env('TWINWORD_API_KEY'); // You need to add this to your .env file
    }

    public function analyze($text)
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Key' => $this->apiKey,
            'X-RapidAPI-Host' => 'twinword-sentiment-analysis.p.rapidapi.com',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->post($this->baseUrl, [
            'text' => $text,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function getSentimentScore($text)
    {
        $result = $this->analyze($text);
        if ($result && isset($result['score'])) {
            return $result['score'];
        }
        return 0; // Default to neutral
    }

    public function getSentimentType($text)
    {
        $result = $this->analyze($text);
        if ($result && isset($result['type'])) {
            return $result['type'];
        }
        return 'neutral';
    }
}
