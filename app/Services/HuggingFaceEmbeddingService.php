<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Hugging Face Inference API service for semantic similarity.
 *
 * Uses a sentence-transformers model to embed job description and resume text,
 * then computes cosine similarity and returns a score 0–100.
 */
class HuggingFaceEmbeddingService
{
    private const API_URL = 'https://api-inference.huggingface.co/models/sentence-transformers/all-MiniLM-L6-v2';

    /**
     * Get embedding vector for a single text via Hugging Face Inference API.
     *
     * @return array<float>|null
     */
    public function getEmbedding(string $text): ?array
    {
        $apiKey = config('services.huggingface.api_key');
        if (empty($apiKey)) {
            Log::warning('HuggingFaceEmbeddingService: HUGGINGFACE_API_KEY is not set.');
            return null;
        }

        $text = trim($text);
        if ($text === '') {
            return null;
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(60)
                ->post(self::API_URL, [
                    'inputs' => $text,
                ]);

            // Serverless models may return 503 "Model is loading" on first request; retry once
            if ($response->status() === 503) {
                sleep(5);
                $response = Http::withToken($apiKey)
                    ->timeout(60)
                    ->post(self::API_URL, ['inputs' => $text]);
            }

            if (! $response->successful()) {
                Log::error('HuggingFaceEmbeddingService: API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();

            // API returns [[float, ...]] for single input
            if (is_array($data) && isset($data[0]) && is_array($data[0])) {
                return $data[0];
            }
            if (is_array($data) && isset($data[0]) && is_numeric($data[0])) {
                return $data;
            }

            Log::warning('HuggingFaceEmbeddingService: Unexpected response shape', ['data_type' => gettype($data)]);
            return null;
        } catch (\Throwable $e) {
            Log::error('HuggingFaceEmbeddingService: Exception', [
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

        $embeddingJob    = $this->getEmbedding($jobDesc);
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
