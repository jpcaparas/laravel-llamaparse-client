# Laravel LlamaParse Cloud API Client

![Tests](https://github.com/jpcaparas/laravel-llamaparse-client/actions/workflows/tests.yml/badge.svg)

![image](https://github.com/user-attachments/assets/7530e6e6-7ed6-4918-ad42-5e38a58ebedf)

This package provides a Laravel library for interacting with the [LlamaCloud *Parsing & Transformation* API (aka *LlamaParse Cloud*)](https://www.llamaindex.ai/blog/introducing-llamacloud-and-llamaparse-af8cedf9006b) to parse PDF, XLS, and other file types into more efficient RAG context for consumption into other systems.

## Installation

You can install the package via Composer:

```bash
composer require jpcaparas/laravel-llamaparse-client
```

After installing the package, publish the configuration file:

```bash
php artisan vendor:publish --provider="JPCaparas\LLamaparse\LLamaparseServiceProvider"
```

Then, add your LlamaParse API key to your `.env` file:

```
LLAMAPARSE_API_KEY=your-api-key
```

## Usage

Here is an example of how to use the package with Laravel Tinker:

1. Set a variable to a path of the file:

```php
$filePath = 'path/to/your/file.pdf';
```

*Hint: [This PDF full of charts](https://www.hunter.cuny.edu/dolciani/pdf_files/workshop-materials/mmc-presentations/tables-charts-and-graphs-with-examples-from.pdf) is a good resource to upload.*

2. Instantiate the service:

```php
$llamaparse = app('llamaparse');
```

3. Upload the file and get the job ID:

```php
$response = $llamaparse->uploadFile($filePath);
$jobId = $response['id'];
```

4. Get the job status:

```php
$jobStatus = $llamaparse->getJob($jobId);
```

5. Get the job markdown result:

```php
$markdownResult = $llamaparse->getJobResult($jobId);
$textResult = $llamaparse->getJobTextResult($jobId);
```

## Running Tests

To run the tests, use the following command:

```bash
composer test
```

## API Reference

For a full API reference, please visit the [LlamaCloud *Parsing & Transformation* API documentation](https://docs.cloud.llamaindex.ai/category/API/parsing).

## License

MIT
