<?php

namespace JPCaparas\LLamaparse\Tests;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use JPCaparas\LLamaparse\LLamaparseClient;
use Orchestra\Testbench\TestCase;

class LlamaparseClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();

        Config::set('llamaparse.api_key', 'test-api-key');
        Config::set('llamaparse.base_url', 'https://api.cloud.llamaindex.ai/api/v1');
        Config::set('llamaparse.timeout', 30);
    }

    public function test_upload_file(): void
    {
        $filePath = 'test.pdf';
        $expectedResponse = [
            'id' => '2ab0a896-561c-425c-842e-7b3b7b7b7b7b',
            'status' => 'PENDING',
        ];

        File::shouldReceive('exists')->with($filePath)->andReturn(true);
        File::shouldReceive('isFile')->with($filePath)->andReturn(true);
        File::shouldReceive('isReadable')->with($filePath)->andReturn(true);
        File::shouldReceive('get')->with($filePath)->andReturn('file contents');

        Http::fake([
            '*/parsing/upload' => Http::response($expectedResponse),
        ]);

        $response = (new LLamaparseClient)->uploadFile($filePath);

        $this->assertEquals($expectedResponse, $response);
    }

    public function test_upload_file_throws_exception_when_file_does_not_exist(): void
    {
        $filePath = 'nonexistent.pdf';

        File::shouldReceive('exists')->with($filePath)->andReturn(false);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("File not found at path: {$filePath}");

        (new LLamaparseClient)->uploadFile($filePath);
    }

    public function test_get_job(): void
    {
        $jobId = '2ab0a896-561c-425c-842e-7b3b7b7b7b7b';
        $expectedResponse = [
            'id' => $jobId,
            'status' => 'SUCCESS',
        ];

        Http::fake([
            "*/parsing/job/{$jobId}" => Http::response($expectedResponse),
        ]);

        $response = (new LLamaparseClient)->getJob($jobId);

        $this->assertEquals($expectedResponse, $response);
    }

    public function test_get_job_text_result(): void
    {
        $jobId = '2ab0a896-561c-425c-842e-7b3b7b7b7b7b';
        $expectedResponse = [
            'text' => 'This is the text extracted from the uploaded file.',
            'job_metadata' => [
                'credits_used' => 34.0,
                'job_credits_usage' => 19,
                'job_pages' => 19,
                'job_auto_mode_triggered_pages' => 0,
                'job_is_cache_hit' => false,
                'credits_max' => 1000,
            ],
        ];

        Http::fake([
            "*/parsing/job/{$jobId}/result/text" => Http::response($expectedResponse),
        ]);

        $response = (new LLamaparseClient)->getJobTextResult($jobId);

        $this->assertEquals($expectedResponse, $response);
    }

    public function test_get_job_result(): void
    {
        $jobId = '2ab0a896-561c-425c-842e-7b3b7b7b7b7b';
        $expectedResponse = [
            'text' => 'This is the _markdown_ generated from the **uploaded** file.',
            'job_metadata' => [
                'credits_used' => 34.0,
                'job_credits_usage' => 19,
                'job_pages' => 19,
                'job_auto_mode_triggered_pages' => 0,
                'job_is_cache_hit' => false,
                'credits_max' => 1000,
            ],
        ];

        Http::fake([
            "*/parsing/job/{$jobId}/result/markdown" => Http::response($expectedResponse),
        ]);

        $response = (new LLamaparseClient)->getJobResult($jobId);

        $this->assertEquals($expectedResponse, $response);
    }
}
