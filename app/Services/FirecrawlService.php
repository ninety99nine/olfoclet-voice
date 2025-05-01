<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class FirecrawlService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.firecrawl.api_key');
        if (!$this->apiKey) {
            throw new \Exception('Firecrawl API key is not configured.');
        }

        $this->client = new Client([
            'base_uri' => 'https://api.firecrawl.dev/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'timeout' => 30,
        ]);
    }

    /**
     * Scrape a single URL using Firecrawl and return the extracted content.
     *
     * @param string $url
     * @return string
     * @throws \Exception
     */
    public function scrapeUrl(string $url): string
    {
        try {
            $response = $this->client->post('scrape', [
                'json' => [
                    'url' => $url,
                    'formats' => ['markdown'],
                    'onlyMainContent' => true,
                    'waitFor' => 5000,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (!$result['success']) {
                throw new \Exception('Firecrawl scrape failed: ' . ($result['error'] ?? 'Unknown error'));
            }

            $content = $result['data']['markdown'] ?? '';
            if (empty($content)) {
                throw new \Exception('No meaningful content extracted from URL: ' . $url);
            }

            Log::info('FirecrawlService: Successfully scraped content', [
                'url' => $url,
                'content_length' => strlen($content),
            ]);

            return $content;
        } catch (GuzzleException $e) {
            Log::error('FirecrawlService: Failed to scrape content', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to scrape content from URL: ' . $e->getMessage());
        }
    }

    /**
     * Start a website crawl using Firecrawl and return the crawl job ID.
     *
     * @param string $url
     * @param int $maxDepth
     * @param int $limit
     * @param string|null $webhookUrl
     * @return string
     * @throws \Exception
     */
    public function startCrawlWebsite(string $url, int $maxDepth = 3, int $limit = 10, ?string $webhookUrl = null): string
    {
        try {
            $payload = [
                'url' => $url,
                'crawlerOptions' => [
                    'maxDepth' => $maxDepth,
                    'limit' => $limit,
                    'formats' => ['markdown'],
                    'onlyMainContent' => true,
                    'waitFor' => 5000,
                ],
            ];

            if ($webhookUrl) {
                $payload['webhook'] = $webhookUrl;
            }

            $response = $this->client->post('crawl', [
                'json' => $payload,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (!$result['success']) {
                throw new \Exception('Firecrawl crawl job failed to start: ' . ($result['error'] ?? 'Unknown error'));
            }

            $jobId = $result['id'] ?? null;
            if (empty($jobId)) {
                throw new \Exception('No crawl job ID returned from Firecrawl');
            }

            Log::info('FirecrawlService: Successfully started crawl job', [
                'url' => $url,
                'job_id' => $jobId,
            ]);

            return $jobId;
        } catch (GuzzleException $e) {
            Log::error('FirecrawlService: Failed to start crawl job', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to start crawl job: ' . $e->getMessage());
        }
    }

    /**
     * Check the status of a crawl job and return the results if completed.
     *
     * @param string $jobId
     * @return array
     * @throws \Exception
     */
    public function checkCrawlStatus(string $jobId): array
    {
        try {
            $response = $this->client->get("crawl/{$jobId}");

            $result = json_decode($response->getBody()->getContents(), true);

            $status = $result['status'] ?? 'unknown';
            $pages = $result['data'] ?? [];
            $nextUrl = $result['next'] ?? null;

            // Handle pagination if the crawl is completed and there are more pages
            while ($status === 'completed' && $nextUrl) {
                $nextResponse = $this->client->get($nextUrl);
                $nextResult = json_decode($nextResponse->getBody()->getContents(), true);
                $pages = array_merge($pages, $nextResult['data'] ?? []);
                $nextUrl = $nextResult['next'] ?? null;
            }

            Log::info('FirecrawlService: Crawl job status checked', [
                'job_id' => $jobId,
                'status' => $status,
                'page_count' => count($pages),
            ]);

            return [
                'status' => $status,
                'pages' => $pages,
            ];
        } catch (GuzzleException $e) {
            Log::error('FirecrawlService: Failed to check crawl job status', [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to check crawl job status: ' . $e->getMessage());
        }
    }

    /**
     * Get the webhook URL for crawl updates.
     *
     * @param string $websiteId
     * @return string
     */
    public function getWebhookUrl(string $websiteId): string
    {
        return route('firecrawl-webhook', ['website' => $websiteId]);
    }
}
