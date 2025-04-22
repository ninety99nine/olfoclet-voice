<?php

namespace App\Services;

use App\Models\MediaFile;
use App\Services\AWSService;
use App\Http\Resources\MediaFileResource;
use App\Http\Resources\MediaFileResources;
use Illuminate\Support\Str;

class MediaFileService extends BaseService
{
    /**
     * @var AWSService
     */
    protected $awsService;

    /**
     * MediaFileService constructor.
     *
     * @param AWSService $awsService
     */
    public function __construct(AWSService $awsService)
    {
        $this->awsService = $awsService;
    }

    /**
     * Show media files.
     *
     * @param string|null $organizationId
     * @return MediaFileResources|array
     */
    public function showMediaFiles(?string $organizationId = null): MediaFileResources|array
    {
        $query = MediaFile::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create media file.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createMediaFile(string $organizationId, array $data): array
    {
        $file = $data['file'];
        $fileName = $data['name'] . '-' . time() . '.' . $file->getClientOriginalExtension();
        $path = "media/{$organizationId}/{$fileName}";

        // Upload to AWS S3
        $this->awsService->uploadFile($file->getPathname(), $path, $file->getClientMimeType());

        $mediaFile = MediaFile::create([
            'name' => $data['name'],
            'file_name' => $fileName,
            'mime_type' => $file->getClientMimeType(),
            'path' => $path,
            'size' => $file->getSize(),
            'organization_id' => $organizationId,
        ]);

        return $this->showCreatedResource($mediaFile);
    }

    /**
     * Show media file.
     *
     * @param string $mediaFileId
     * @return MediaFileResource
     */
    public function showMediaFile(string $mediaFileId): MediaFileResource
    {
        $mediaFile = MediaFile::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($mediaFileId);
        return $this->showResource($mediaFile);
    }

    /**
     * Update media file.
     *
     * @param string $mediaFileId
     * @param array $data
     * @return array
     */
    public function updateMediaFile(string $mediaFileId, array $data): array
    {
        $mediaFile = MediaFile::findOrFail($mediaFileId);

        if (isset($data['file'])) {
            // Delete old file from S3
            $this->awsService->deleteFile($mediaFile->path);

            // Upload new file
            $file = $data['file'];
            $fileName = $data['name'] . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = "media/{$mediaFile->organization_id}/{$fileName}";
            $this->awsService->uploadFile($file->getPathname(), $path, $file->getClientMimeType());

            $data['file_name'] = $fileName;
            $data['mime_type'] = $file->getClientMimeType();
            $data['path'] = $path;
            $data['size'] = $file->getSize();
        }

        $mediaFile->update($data);
        return $this->showUpdatedResource($mediaFile);
    }

    /**
     * Delete media file.
     *
     * @param string $mediaFileId
     * @return array
     */
    public function deleteMediaFile(string $mediaFileId): array
    {
        $mediaFile = MediaFile::findOrFail($mediaFileId);

        // Delete file from S3
        $this->awsService->deleteFile($mediaFile->path);

        $deleted = $mediaFile->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Media file deleted'];
        }

        return ['deleted' => false, 'message' => 'Media file delete unsuccessful'];
    }

    /**
     * Delete media files.
     *
     * @param string|null $organizationId
     * @param array $mediaFileIds
     * @return array
     */
    public function deleteMediaFiles(?string $organizationId, array $mediaFileIds): array
    {
        $query = MediaFile::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->whereIn('id', $mediaFileIds);

        $mediaFiles = $query->get();

        if ($totalMediaFiles = $mediaFiles->count()) {
            $mediaFiles->each(function ($mediaFile) {
                $this->awsService->deleteFile($mediaFile->path);
                $mediaFile->delete();
            });
            return ['deleted' => true, 'message' => "$totalMediaFiles media file(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No media files deleted'];
    }

    /**
     * Get a pre-signed URL for the media file.
     *
     * @param string $mediaFileId
     * @return array
     */
    public function getPresignedUrl(string $mediaFileId): array
    {
        $mediaFile = MediaFile::findOrFail($mediaFileId);
        $url = $this->awsService->getPresignedUrl($mediaFile->path, 60); // URL expires in 60 minutes
        return ['url' => $url];
    }
}
