<?php

return [
    'api_key' => env('LLAMAPARSE_API_KEY', ''),
    'base_url' => env('LLAMAPARSE_BASE_URL', 'https://api.cloud.llamaindex.ai/api/v1'),
    'timeout' => env('LLAMAPARSE_TIMEOUT', 30),
];
