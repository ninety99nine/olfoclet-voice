<?php

namespace App\Services;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class AWSService
{
    /**
     * @var S3Client
     */
    protected $s3Client;

    /**
     * AWSService constructor.
     */
    public function __construct()
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => config('services.aws.region'),
            'credentials' => [
                'key' => config('services.aws.access_key_id'),
                'secret' => config('services.aws.secret_access_key'),
            ],
        ]);
    }

    /**
     * Upload a file to AWS S3.
     *
     * @param string $filePath
     * @param string $s3Path
     * @param string $contentType
     * @return void
     * @throws \Exception
     */
    public function uploadFile(string $filePath, string $s3Path, string $contentType): void
    {
        try {
            $this->s3Client->putObject([
                'Bucket' => config('services.aws.bucket'),
                'Key' => $s3Path,
                'SourceFile' => $filePath,
                'ContentType' => $contentType,
                'ACL' => 'private',
            ]);
        } catch (AwsException $e) {
            throw new \Exception('Failed to upload file to S3: ' . $e->getMessage());
        }
    }

    /**
     * Delete a file from AWS S3.
     *
     * @param string $s3Path
     * @return void
     * @throws \Exception
     */
    public function deleteFile(string $s3Path): void
    {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => config('services.aws.bucket'),
                'Key' => $s3Path,
            ]);
        } catch (AwsException $e) {
            throw new \Exception('Failed to delete file from S3: ' . $e->getMessage());
        }
    }

    /**
     * Check if a file exists in AWS S3.
     *
     * @param string $s3Path
     * @return bool
     */
    public function fileExists(string $s3Path): bool
    {
        try {
            return $this->s3Client->doesObjectExist(
                config('services.aws.bucket'),
                $s3Path
            );
        } catch (AwsException $e) {
            return false; // Return false if there's an error (e.g., bucket not accessible)
        }
    }

    /**
     * Get a pre-signed URL for accessing a file in AWS S3.
     *
     * @param string $s3Path
     * @param int $expirationMinutes
     * @return string
     * @throws \Exception
     */
    public function getPresignedUrl(string $s3Path, int $expirationMinutes = 60): string
    {
        try {
            $cmd = $this->s3Client->getCommand('GetObject', [
                'Bucket' => config('services.aws.bucket'),
                'Key' => $s3Path,
            ]);

            $request = $this->s3Client->createPresignedRequest($cmd, "+{$expirationMinutes} minutes");
            return (string) $request->getUri();
        } catch (AwsException $e) {
            throw new \Exception('Failed to generate pre-signed URL: ' . $e->getMessage());
        }
    }
}
