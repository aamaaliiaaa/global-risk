<?php

namespace App\Services;

use App\Models\PositiveWord;
use App\Models\NegativeWord;

class SentimentAnalysisService
{
    /**
     * Analyze text sentiment using database-backed lexicon dictionaries.
     *
     * @param string $text
     * @return array
     */
    public function analyze(string $text): array
    {
        $text = strtolower($text);
        
        // Remove punctuation and common symbols
        $text = preg_replace('/[^\w\s]/', '', $text);
        
        // Tokenize words
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Fetch dictionaries
        $positiveWords = PositiveWord::pluck('word')->toArray();
        $negativeWords = NegativeWord::pluck('word')->toArray();

        $posCount = 0;
        $negCount = 0;
        $matchedPos = [];
        $matchedNeg = [];

        foreach ($words as $word) {
            if (in_array($word, $positiveWords)) {
                $posCount++;
                $matchedPos[] = $word;
            }
            if (in_array($word, $negativeWords)) {
                $negCount++;
                $matchedNeg[] = $word;
            }
        }

        $sentiment = 'Neutral';
        if ($posCount > $negCount) {
            $sentiment = 'Positive';
        } elseif ($negCount > $posCount) {
            $sentiment = 'Negative';
        }

        return [
            'sentiment' => $sentiment,
            'positive_count' => $posCount,
            'negative_count' => $negCount,
            'matched_positive' => array_unique($matchedPos),
            'matched_negative' => array_unique($matchedNeg),
        ];
    }
}
