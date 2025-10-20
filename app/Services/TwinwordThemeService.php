<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TwinwordThemeService
{
    protected $apiKey;
    protected $baseUrl = 'https://twinword-twinword-bundle-v1.p.rapidapi.com/word_theme/';

    public function __construct()
    {
        $this->apiKey = env('TWINWORD_API_KEY'); // Using the same key as sentiment
    }

    public function analyze($entry)
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Key' => $this->apiKey,
            'X-RapidAPI-Host' => 'twinword-text-analysis-bundle.p.rapidapi.com',
        ])->get($this->baseUrl, [
            'entry' => $entry,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    public function getThemes($text)
    {
        $result = $this->analyze($text);
        if ($result && isset($result['themes'])) {
            return $result['themes'];
        }
        return [];
    }
}
