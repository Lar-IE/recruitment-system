<?php

/**
 * OpenAI Embeddings API — PHP examples using cURL.
 *
 * These are standalone examples for reference. The app uses
 * App\Services\OpenAiEmbeddingService with Laravel HTTP client.
 *
 * Usage (from project root):
 *   php docs/OpenAiCurlExamples.php
 *
 * The script loads OPENAI_API_KEY from your .env file. Alternatively:
 *   PowerShell: $env:OPENAI_API_KEY = "sk-..."; php docs/OpenAiCurlExamples.php
 *   CMD:        set OPENAI_API_KEY=sk-... && php docs/OpenAiCurlExamples.php
 */

// Load OPENAI_API_KEY from .env when run from project root
$apiKey = getenv('OPENAI_API_KEY');
if (empty($apiKey)) {
    $envPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
    if (is_file($envPath)) {
        $lines = @file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '' && strpos($line, '#') !== 0 && strpos($line, 'OPENAI_API_KEY=') === 0) {
                $apiKey = trim(substr($line, strlen('OPENAI_API_KEY=')), " \t\"'");
                break;
            }
        }
    }
}
$apiKey = $apiKey ?: '';

$apiUrl = 'https://api.openai.com/v1/embeddings';
$model  = 'text-embedding-3-small';

// -----------------------------------------------------------------------------
// Example 1: Get embedding for a single text (cURL)
// -----------------------------------------------------------------------------
function getEmbeddingCurl(string $text, string $apiKey, string $apiUrl, string $model): ?array
{
    $payload = json_encode([
        'model' => $model,
        'input' => $text,
    ]);

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST          => true,
        CURLOPT_HTTPHEADER    => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_POSTFIELDS    => $payload,
        CURLOPT_TIMEOUT       => 30,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || $response === false) {
        return null;
    }

    $data = json_decode($response, true);
    return $data['data'][0]['embedding'] ?? null;
}

// -----------------------------------------------------------------------------
// Example 2: Cosine similarity between two vectors
// -----------------------------------------------------------------------------
function cosineSimilarity(array $a, array $b): float
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
    return $denom > 0 ? $dot / $denom : 0.0;
}

// -----------------------------------------------------------------------------
// Example 3: Semantic score 0–100 from job description and resume text
// -----------------------------------------------------------------------------
function getSemanticScoreCurl(string $jobDescription, string $resumeText, string $apiKey, string $apiUrl, string $model): ?float
{
    $resumeText = mb_substr(trim($resumeText), 0, 1500);

    $embeddingJob   = getEmbeddingCurl(trim($jobDescription), $apiKey, $apiUrl, $model);
    $embeddingResume = getEmbeddingCurl($resumeText, $apiKey, $apiUrl, $model);

    if ($embeddingJob === null || $embeddingResume === null) {
        return null;
    }

    $similarity = cosineSimilarity($embeddingJob, $embeddingResume);
    $score = ($similarity + 1) / 2 * 100; // map [-1,1] to [0,100]
    return min(100.0, max(0.0, round($score, 2)));
}

// -----------------------------------------------------------------------------
// Run example (only if executed directly)
// -----------------------------------------------------------------------------
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    if (empty($apiKey) || $apiKey === 'sk-your-key-here') {
        echo "OPENAI_API_KEY is not set. Add it to your .env file:\n";
        echo "  OPENAI_API_KEY=sk-your-actual-openai-key\n";
        echo "Then run from project root: php docs/OpenAiCurlExamples.php\n";
        exit(1);
    }

    $jobDesc = 'Software Engineer with PHP and Laravel experience.';
    $resume  = 'I have 3 years of PHP and Laravel development experience.';

    echo "Job description: $jobDesc\n";
    echo "Resume excerpt:   $resume\n\n";

    $score = getSemanticScoreCurl($jobDesc, $resume, $apiKey, $apiUrl, $model);
    if ($score !== null) {
        echo "Semantic score (0–100): $score\n";
    } else {
        echo "Failed to get score (check OPENAI_API_KEY in .env and network).\n";
    }
}
