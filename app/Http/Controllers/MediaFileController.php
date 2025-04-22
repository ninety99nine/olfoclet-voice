<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use Illuminate\Http\JsonResponse;
use App\Services\MediaFileService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\MediaFileResources;
use App\Http\Requests\MediaFile\ShowMediaFileRequest;
use App\Http\Requests\MediaFile\ShowMediaFilesRequest;
use App\Http\Requests\MediaFile\CreateMediaFileRequest;
use App\Http\Requests\MediaFile\UpdateMediaFileRequest;
use App\Http\Requests\MediaFile\DeleteMediaFileRequest;
use App\Http\Requests\MediaFile\DeleteMediaFilesRequest;

class MediaFileController extends BaseController
{
    /**
     * @var MediaFileService
     */
    protected $service;

    /**
     * MediaFileController constructor.
     *
     * @param MediaFileService $service
     */
    public function __construct(MediaFileService $service)
    {
        $this->service = $service;
    }

    /**
     * Show media files.
     *
     * @param ShowMediaFilesRequest $request
     * @return MediaFileResources|JsonResponse
     */
    public function showMediaFiles(ShowMediaFilesRequest $request): MediaFileResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showMediaFiles(request('organization_id')));
    }

    /**
     * Create media file.
     *
     * @param CreateMediaFileRequest $request
     * @return JsonResponse
     */
    public function createMediaFile(CreateMediaFileRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createMediaFile(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Delete multiple media files.
     *
     * @param DeleteMediaFilesRequest $request
     * @return JsonResponse
     */
    public function deleteMediaFiles(DeleteMediaFilesRequest $request): JsonResponse
    {
        $mediaFileIds = request()->input('media_file_ids', []);
        return $this->prepareOutput($this->service->deleteMediaFiles(request('organization_id'), $mediaFileIds));
    }

    /**
     * Show single media file.
     *
     * @param ShowMediaFileRequest $request
     * @param MediaFile $mediaFile
     * @return JsonResponse
     */
    public function showMediaFile(ShowMediaFileRequest $request, MediaFile $mediaFile): JsonResponse
    {
        return $this->prepareOutput($this->service->showMediaFile($mediaFile->id));
    }

    /**
     * Update media file.
     *
     * @param UpdateMediaFileRequest $request
     * @param MediaFile $mediaFile
     * @return JsonResponse
     */
    public function updateMediaFile(UpdateMediaFileRequest $request, MediaFile $mediaFile): JsonResponse
    {
        return $this->prepareOutput($this->service->updateMediaFile($mediaFile->id, $request->validated()));
    }

    /**
     * Delete media file.
     *
     * @param DeleteMediaFileRequest $request
     * @param MediaFile $mediaFile
     * @return JsonResponse
     */
    public function deleteMediaFile(DeleteMediaFileRequest $request, MediaFile $mediaFile): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteMediaFile($mediaFile->id));
    }

    /**
     * Get a pre-signed URL for the media file.
     *
     * @param ShowMediaFileRequest $request
     * @param MediaFile $mediaFile
     * @return JsonResponse
     */
    public function getPresignedUrl(ShowMediaFileRequest $request, MediaFile $mediaFile): JsonResponse
    {
        return $this->prepareOutput($this->service->getPresignedUrl($mediaFile->id));
    }
}
