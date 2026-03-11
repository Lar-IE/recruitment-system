<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * OpenAI Embeddings API service for semantic similarity.
 *
 * Uses text-embedding-3-small to embed job description and resume text,
 * then computes cosine similarity and returns a score 0–100.
 */
class OpenAiEmbeddingService
{
    private const API_URL = 'https://api.openai.com/v1/embeddings';
    private const MODEL   = 'text-embedding-3-small';

    /**
     * Get embedding vector for a single text via OpenAI API.
     *
     * @return array<float>|null
     */
    public function getEmbedding(string $text): ?array
    {
        $apiKey = config('services.openai.api_key');
        if (empty($apiKey)) {
            Log::warning('OpenAiEmbeddingService: OPENAI_API_KEY is not set.');
            return null;
        }

        $text = trim($text);
        if ($text === '') {
            return null;
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post(self::API_URL, [
                    'model' => self::MODEL,
                    'input' => $text,
                ]);

            if (! $response->successful()) {
                Log::error('OpenAiEmbeddingService: OpenAI API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            $embedding = $data['data'][0]['embedding'] ?? null;

            return is_array($embedding) ? $embedding : null;
        } catch (\Throwable $e) {
            Log::error('OpenAiEmbeddingService: Exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Compute cosine similarity between two vectors.
     * Returns value in [-1, 1]. Semantic similarity is typically in [0, 1].
     */
    public function cosineSimilarity(array $a, array $b): float
    {
        if (count($a) !== count($b) || count($a) === 0) {
            return 0.0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $i => $va) {
            $vb = $b[$i] ?? 0;
            $dot += $va * $vb;
            $normA += $va * $va;
            $normB += $vb * $vb;
        }

        $denom = sqrt($normA) * sqrt($normB);
        if ($denom <= 0) {
            return 0.0;
        }

        return $dot / $denom;
    }

    /**
     * Semantic similarity score (0–100) between job description and resume text.
     * Uses embeddings + cosine similarity; clamps result to 0–100.
     *
     * Returns null on API failure (caller should use rule-only fallback).
     */
    public function getSemanticScore(string $jobDescription, string $resumeText): ?float
    {
        $jobDesc = trim($jobDescription);
        $resume  = trim($resumeText);

        if ($jobDesc === '' || $resume === '') {
            return null;
        }

        $embeddingJob   = $this->getEmbedding($jobDesc);
        $embeddingResume = $this->getEmbedding($resume);

        if ($embeddingJob === null || $embeddingResume === null) {
            return null;
        }

        $similarity = $this->cosineSimilarity($embeddingJob, $embeddingResume);

        // Cosine similarity is in [-1, 1]. Map to 0–100.
        $score = ($similarity + 1) / 2 * 100;

        return min(100.0, max(0.0, round($score, 2)));
    }
}
