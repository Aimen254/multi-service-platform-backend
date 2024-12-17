<?php

namespace App\Services;

use GuzzleHttp\Client;

class ChatGPTService
{
    protected Client $client;
    protected string $apiKey;
    protected string $baseUrl = 'https://api.openai.com/v1';

    /**
     * Adjust the following parameters to match your desired results
     */
    protected float $topP = 0.1;
    protected int $maxTokens = 5;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('CHAT_GPT_API_KEY');
    }

    public function getCategory(array $categories, array $product): string
    {
        $categories = implode(', ', $categories);

        $response = $this->client->post("{$this->baseUrl}/chat/completions", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$this->apiKey}",
            ],
            'json' => [
                'model' => 'gpt-4-0613',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "You are tasked with selecting the appropriate category for a product title or description. 
                        You may only select one category from the list of categories I provided below. 
                        Only return the category name. 
                        
                        Categories: {$categories}
                        
                        Product Title: {$product['title']}
                        
                        Product Description: {$product['description']}"
                    ]
                ],
                'top_p' => 0.1,
                'max_tokens' => 10
            ],
            'verify' => config('app.env') == 'production'
        ]);

        $responseData = json_decode($response->getBody(), true);

        if (!isset($responseData['choices']) || empty($responseData['choices'])) {
            throw new \Exception('An error occurred while getting category from ChatGPT.');
        }

        return $responseData['choices'][0]['message']['content'];
    }
}
