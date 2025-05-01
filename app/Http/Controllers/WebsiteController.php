<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\JsonResponse;
use App\Services\WebsiteService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\WebsiteResources;
use App\Http\Requests\Website\ShowWebsitesRequest;
use App\Http\Requests\Website\CreateWebsiteRequest;
use App\Http\Requests\Website\ShowWebsiteRequest;
use App\Http\Requests\Website\UpdateWebsiteRequest;
use App\Http\Requests\Website\DeleteWebsiteRequest;
use App\Http\Requests\Website\CheckCrawlStatusRequest;
use App\Http\Requests\Website\DeleteWebsitesRequest;

class WebsiteController extends BaseController
{
    /**
     * @var WebsiteService
     */
    protected $service;

    /**
     * WebsiteController constructor.
     *
     * @param WebsiteService $service
     */
    public function __construct(WebsiteService $service)
    {
        $this->service = $service;
    }

    /**
     * Show websites.
     *
     * @param ShowWebsitesRequest $request
     * @return WebsiteResources|JsonResponse
     */
    public function showWebsites(ShowWebsitesRequest $request): WebsiteResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showWebsites(request('organization_id')));
    }

    /**
     * Create website.
     *
     * @param CreateWebsiteRequest $request
     * @return JsonResponse
     */
    public function createWebsite(CreateWebsiteRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createWebsite(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Show single website.
     *
     * @param ShowWebsiteRequest $request
     * @param Website $website
     * @return JsonResponse
     */
    public function showWebsite(ShowWebsiteRequest $request, Website $website): JsonResponse
    {
        return $this->prepareOutput($this->service->showWebsite($website->id));
    }

    /**
     * Check crawl status for a website.
     *
     * @param CheckCrawlStatusRequest $request
     * @param Website $website
     * @return JsonResponse
     */
    public function checkCrawlStatus(CheckCrawlStatusRequest $request, Website $website): JsonResponse
    {
        return $this->prepareOutput($this->service->checkCrawlStatus($website));
    }

    /**
     * Handle Firecrawl webhook updates.
     *
     * @param Website $website
     * @return JsonResponse
     */
    public function handleWebhook(Website $website): JsonResponse
    {
        return $this->prepareOutput($this->service->handleWebhook($website));
    }

    /**
     * Update website.
     *
     * @param UpdateWebsiteRequest $request
     * @param Website $website
     * @return JsonResponse
     */
    public function updateWebsite(UpdateWebsiteRequest $request, Website $website): JsonResponse
    {
        return $this->prepareOutput($this->service->updateWebsite($website->id, $request->validated()));
    }

    /**
     * Delete website.
     *
     * @param DeleteWebsiteRequest $request
     * @param Website $website
     * @return JsonResponse
     */
    public function deleteWebsite(DeleteWebsiteRequest $request, Website $website): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteWebsite($website->id));
    }

    /**
     * Delete multiple websites.
     *
     * @param DeleteWebsitesRequest $request
     * @return JsonResponse
     */
    public function deleteWebsites(DeleteWebsitesRequest $request): JsonResponse
    {
        $websiteIds = request()->input('website_ids', []);
        return $this->prepareOutput($this->service->deleteWebsites(request('organization_id'), $websiteIds));
    }
}
