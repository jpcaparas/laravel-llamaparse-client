<?php

namespace JPCaparas\LLamaparse;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class LLamaparseClient
{
    protected PendingRequest $client;
    protected $apiKey;
    protected $baseUrl;
    protected $timeout;

    public function __construct()
    {
        $this->apiKey = config('llamaparse.api_key');
        $this->baseUrl = config('llamaparse.base_url');
        $this->timeout = config('llamaparse.timeout');

        $this->client = Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->withToken($this->apiKey);
    }

    /**
     * Example response:
     * [
     *   "id" => "2ab0a896-561c-425c-842e-7b3b7b7b7b7b",
     *   "status" => "PENDING"
     * ]
     */
    public function uploadFile($filePath, bool $shouldProvideStructuredOutput = true): array
    {
        if (!File::exists($filePath)) {
            throw new Exception("File not found at path: {$filePath}");
        }

        if (!File::isFile($filePath)) {
            throw new Exception("Path is not a file: {$filePath}");
        }

        if (!File::isReadable($filePath)) {
            throw new Exception("File is not readable: {$filePath}");
        }

        $response = $this->client->asMultipart()->post('/parsing/upload', [
            'file' => fopen($filePath, 'r'),
            'structured_output' => $shouldProvideStructuredOutput,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Example response:
     * [
     *   "id" => "2ab0a896-561c-425c-842e-7b3b7b7b7b7b",
     *   "status" => "SUCCESS"
     * ]
     */
    public function getJob(string $jobId): array
    {
        $response = $this->client->get("/parsing/job/{$jobId}");

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Example response:
     * [
     *   "text" => "This is the text extracted from the uploaded file.",
     *   "job_metadata" => [
     *     "credits_used" => 34.0,
     *     "job_credits_usage" => 19,
     *     "job_pages" => 19,
     *     "job_auto_mode_triggered_pages" => 0,
     *     "job_is_cache_hit" => false,
     *     "credits_max" => 1000,
     *    ]
     * ]
     */
    public function getJobTextResult(string $jobId): array
    {
        $response = $this->client->get("/parsing/job/{$jobId}/result/text");

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Example response:
     * [
     *   "text" => "This is the _markdown_ generated from the **uploaded** file.",
     *   "job_metadata" => [
     *     "credits_used" => 34.0,
     *     "job_credits_usage" => 19,
     *     "job_pages" => 19,
     *     "job_auto_mode_triggered_pages" => 0,
     *     "job_is_cache_hit" => false,
     *     "credits_max" => 1000,
     *    ]
     * ]
     */
    public function getJobResult(string $jobId): array
    {
        $response = $this->client->get("/parsing/job/{$jobId}/result/markdown");

        return json_decode($response->getBody()->getContents(), true);
    }
}
