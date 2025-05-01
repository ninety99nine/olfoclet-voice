<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OpenAiService
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * OpenAiService constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Generate an embedding for a given text using OpenAI's API.
     *
     * @param string $text
     * @param string|null $model
     * @return array
     * @throws \Exception
     */
    public function generateEmbedding(string $text, string $model = null): array
    {
        try {
            $response = $this->client->post('embeddings', [
                'json' => [
                    'input' => $text,
                    'model' => $model ?? config('services.openai.embedding_model'),
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            return $result['data'][0]['embedding'] ?? [];
        } catch (GuzzleException $e) {
            throw new \Exception('Failed to generate embedding with OpenAI: ' . $e->getMessage());
        }
    }

    /**
     * Classify whether a query requires external sources (RAG).
     *
     * @param string $query
     * @param string|null $model
     * @param float $temperature
     * @return array
     * @throws \Exception
     */
    public function classifyQueryIntent(string $query, string $model = null, float $temperature = 0.7): array
    {
        // System prompt for intent classification
        $systemPrompt = "You are a helpful assistant for a call center platform. Your task is to determine if the user's query requires specific knowledge from external sources to be answered accurately. Examples of queries needing sources include those asking for specific procedures, technical instructions, or detailed information (e.g., 'How do I reset my SIM card?', 'What are the steps to troubleshoot network issues?'). Queries that are conversational, general, or don't require specific knowledge (e.g., 'Hello,' 'Thank you,' 'How are you') typically do not require sources. Respond with a JSON object containing a single boolean field 'needsSources' indicating whether the query requires external sources. Provide only the JSON object, no additional text.";

        $userPrompt = "Query: $query";

        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => $model ?? config('services.openai.prompt_model'),
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'temperature' => $temperature,
                    'max_tokens' => 50 // Short response for intent classification
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            $content = $result['choices'][0]['message']['content'] ?? '{}';
            $parsedResult = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE || !isset($parsedResult['needsSources'])) {
                throw new \Exception('Invalid intent classification response from OpenAI');
            }
            return $parsedResult;
        } catch (GuzzleException $e) {
            throw new \Exception('Failed to classify query intent with OpenAI: ' . $e->getMessage());
        }
    }

    /**
     * Generate a response using OpenAI's chat completion API with retrieved context (RAG).
     *
     * @param string $query
     * @param string $context
     * @param string|null $model
     * @param float $temperature
     * @return string
     * @throws \Exception
     */
    public function promptWithContext(string $query, string $context, string $model = null, float $temperature = 0.7): string
    {
        // System prompt for response generation
        $systemPrompt = "You are a helpful assistant for a call center platform. Use the following context to answer the user's query accurately. If context is provided, ensure your response is grounded in the given context. If no context is provided, respond naturally using general knowledge appropriate for a call center assistant. Avoid making assumptions not supported by the context when context is available. Format your response in Markdown, using headings, lists, bold/italic text, code blocks, or other Markdown syntax where appropriate to improve readability and structure.";

        // Construct the user prompt with context and query
        $contextContent = $context ? json_decode($context, true) : [];
        $contextText = !empty($contextContent) ? json_encode($contextContent, JSON_PRETTY_PRINT) : 'No context available.';
        $userPrompt = "Context:\n---\n$contextText\n---\n\nQuery: $query";

        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => $model ?? config('services.openai.prompt_model'),
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'temperature' => $temperature,
                    'max_tokens' => 1000    // Adjust based on your needs
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            return $result['choices'][0]['message']['content'] ?? '';
        } catch (GuzzleException $e) {
            throw new \Exception('Failed to generate response with OpenAI: ' . $e->getMessage());
        }
    }
}
