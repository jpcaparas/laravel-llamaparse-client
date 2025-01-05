<?php

namespace JPCaparas\LLamaparse\Tests;

use JPCaparas\LLamaparse\LLamaparseClient;
use Orchestra\Testbench\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class LLamaparseClientTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return ['JPCaparas\LLamaparse\LLamaparseServiceProvider'];
    }

    public function testUploadFile()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['job_id' => '12345']))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $llamaparseClient = new LLamaparseClient();
        $llamaparseClient->client = $client;

        $response = $llamaparseClient->uploadFile('path/to/file.pdf');

        $this->assertArrayHasKey('job_id', $response);
        $this->assertEquals('12345', $response['job_id']);
    }

    public function testGetJob()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['status' => 'completed']))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $llamaparseClient = new LLamaparseClient();
        $llamaparseClient->client = $client;

        $response = $llamaparseClient->getJob('12345');

        $this->assertArrayHasKey('status', $response);
        $this->assertEquals('completed', $response['status']);
    }

    public function testGetJobMarkdownResult()
    {
        $mock = new MockHandler([
            new Response(200, [], 'Markdown content')
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $llamaparseClient = new LLamaparseClient();
        $llamaparseClient->client = $client;

        $response = $llamaparseClient->getJobMarkdownResult('12345');

        $this->assertEquals('Markdown content', $response);
    }

    public function testGetSupportedFileExtensions()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['extensions' => ['pdf', 'xls']]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $llamaparseClient = new LLamaparseClient();
        $llamaparseClient->client = $client;

        $response = $llamaparseClient->getSupportedFileExtensions();

        $this->assertArrayHasKey('extensions', $response);
        $this->assertEquals(['pdf', 'xls'], $response['extensions']);
    }
}
