<?php

namespace App\Services;

use App\Models\Website;
use App\Models\WebsitePage;
use App\Http\Resources\WebsiteResource;
use App\Http\Resources\WebsiteResources;
use Ramsey\Uuid\Uuid;

class WebsiteService extends BaseService
{
    /**
     * @var FirecrawlService
     */
    protected $firecrawlService;

    /**
     * @var OpenAiService
     */
    protected $openAiService;

    /**
     * @var PineconeService
     */
    protected $pineconeService;

    /**
     * WebsiteService constructor.
     *
     * @param FirecrawlService $firecrawlService
     * @param OpenAiService $openAiService
     * @param PineconeService $pineconeService
     */
    public function __construct(FirecrawlService $firecrawlService, OpenAiService $openAiService, PineconeService $pineconeService)
    {
        $this->firecrawlService = $firecrawlService;
        $this->openAiService = $openAiService;
        $this->pineconeService = $pineconeService;
    }

    /**
     * Show websites.
     *
     * @param string|null $organizationId
     * @return WebsiteResources|array
     */
    public function showWebsites(?string $organizationId = null): WebsiteResources|array
    {
        $query = Website::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create website and start a crawl job.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createWebsite(string $organizationId, array $data): array
    {
        $data['organization_id'] = $organizationId;
        $data['sync_status'] = 'syncing';
        $website = Website::create($data);

        // Start the crawl job with a webhook
        $webhookUrl = $this->firecrawlService->getWebhookUrl($website->id);
        $jobId = $this->firecrawlService->startCrawlWebsite($website->url, 2, 10, $webhookUrl);

        // Store the crawl job ID in metadata (you may need to add a column or metadata field)
        $website->update(['metadata' => array_merge($website->metadata ?? [], ['crawl_job_id' => $jobId])]);

        return $this->showCreatedResource($website);
    }

    /**
     * Show website.
     *
     * @param string $websiteId
     * @return WebsiteResource
     */
    public function showWebsite(string $websiteId): WebsiteResource
    {
        $website = Website::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($websiteId);
        return $this->showResource($website);
    }

    /**
     * Check the crawl status for a website.
     *
     * @param Website $website
     * @return array
     */
    public function checkCrawlStatus(Website $website): array
    {
        $jobId = $website->metadata['crawl_job_id'] ?? null;
        if (!$jobId) {
            return ['status' => 'failed', 'message' => 'No crawl job found for this website'];
        }

        $result = $this->firecrawlService->checkCrawlStatus($jobId);
        $status = $result['status'];
        $pages = $result['pages'];

        if ($status === 'completed' && !empty($pages)) {
            $this->processCrawlResults($website, $pages);
            $website->update(['sync_status' => 'completed', 'last_synced_at' => now()]);
        }

        return [
            'status' => $status,
            'page_count' => count($pages),
        ];
    }

    /**
     * Handle Firecrawl webhook updates.
     *
     * @param Website $website
     * @return array
     */
    public function handleWebhook(Website $website): array
    {
        $payload = request()->all();
        $eventType = $payload['type'] ?? 'unknown';
        $data = $payload['data'] ?? [];

        if ($eventType === 'crawl.started') {
            $website->update(['sync_status' => 'syncing']);
            return ['success' => true, 'message' => 'Crawl started'];
        }

        if ($eventType === 'crawl.page' && !empty($data)) {
            $this->processCrawlResults($website, $data);
            return ['success' => true, 'message' => 'Page processed'];
        }

        if ($eventType === 'crawl.completed') {
            $website->update(['sync_status' => 'completed', 'last_synced_at' => now()]);
            return ['success' => true, 'message' => 'Crawl completed'];
        }

        if ($eventType === 'crawl.failed') {
            $website->update(['sync_status' => 'failed']);
            return ['success' => false, 'message' => 'Crawl failed: ' . ($payload['error'] ?? 'Unknown error')];
        }

        return ['success' => false, 'message' => 'Unknown event type'];
    }

    /**
     * Process crawl results and upsert pages to Pinecone.
     *
     * @param Website $website
     * @param array $pages
     * @return void
     */
    protected function processCrawlResults(Website $website, array $pages): void
    {
        foreach ($pages as $page) {
            $pageUrl = $page['url'] ?? '';
            $content = $page['markdown'] ?? '';
            if (empty($pageUrl) || empty($content)) {
                continue;
            }

            $websitePage = WebsitePage::updateOrCreate(
                ['website_id' => $website->id, 'page_url' => $pageUrl],
                [
                    'id' => Uuid::uuid4()->toString(),
                    'content' => $content,
                    'ai_searchable' => $website->ai_searchable,
                    'organization_id' => $website->organization_id,
                ]
            );

            if ($websitePage->ai_searchable) {
                $embedding = $this->openAiService->generateEmbedding($content);
                $metadata = [
                    'knowledge_base_id' => $website->knowledge_base_id,
                    'type' => 'website_page',
                    'content' => $content,
                    'page_url' => $pageUrl,
                    'website_url' => $website->url,
                    'tags' => ['website_page', 'orange_botswana'],
                ];
                $this->pineconeService->upsertVector($websitePage->id, $embedding, $website->organization_id, $metadata);
            }
        }
    }

    /**
     * Update website.
     *
     * @param string $websiteId
     * @param array $data
     * @return array
     */
    public function updateWebsite(string $websiteId, array $data): array
    {
        $website = Website::findOrFail($websiteId);
        $oldAiSearchable = $website->ai_searchable;
        $website->update($data);

        // Handle Pinecone updates for RAG if ai_searchable changes
        if (isset($data['ai_searchable'])) {
            if (!$website->ai_searchable && $oldAiSearchable) {
                // Delete associated website pages from Pinecone
                $website->pages()->where('ai_searchable', true)->get()->each(function ($page) {
                    $this->pineconeService->deleteVector($page->id, $page->organization_id);
                });
            } elseif ($website->ai_searchable && !$oldAiSearchable) {
                // Re-sync the website to re-index pages
                $website->update(['sync_status' => 'pending']);
                $jobId = $this->firecrawlService->startCrawlWebsite(
                    $website->url,
                    2,
                    10,
                    $this->firecrawlService->getWebhookUrl($website->id)
                );
                $website->update(['metadata' => array_merge($website->metadata ?? [], ['crawl_job_id' => $jobId])]);
            }
        }

        return $this->showUpdatedResource($website);
    }

    /**
     * Delete website.
     *
     * @param string $websiteId
     * @return array
     */
    public function deleteWebsite(string $websiteId): array
    {
        $website = Website::findOrFail($websiteId);

        // Delete associated website pages from Pinecone
        $website->pages()->where('ai_searchable', true)->get()->each(function ($page) {
            $this->pineconeService->deleteVector($page->id, $page->organization_id);
        });

        $deleted = $website->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Website deleted'];
        }

        return ['deleted' => false, 'message' => 'Website delete unsuccessful'];
    }

    /**
     * Delete websites.
     *
     * @param string|null $organizationId
     * @param array $websiteIds
     * @return array
     */
    public function deleteWebsites(?string $organizationId, array $websiteIds): array
    {
        $query = Website::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->whereIn('id', $websiteIds);

        $websites = $query->get();

        if ($totalWebsites = $websites->count()) {
            // Delete associated website pages from Pinecone
            foreach ($websites as $website) {
                $website->pages()->where('ai_searchable', true)->get()->each(function ($page) {
                    $this->pineconeService->deleteVector($page->id, $page->organization_id);
                });
            }

            $websites->each->delete();
            return ['deleted' => true, 'message' => "$totalWebsites website(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No websites deleted'];
    }
}
