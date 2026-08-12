<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class NextcloudService
{
    private string $baseUrl;

    private string $username;

    private string $appPassword;

    public function __construct()
    {
        $webDavUrl = config('services.nextcloud.url');
        $username = config('services.nextcloud.username');
        $appPassword = config('services.nextcloud.app_password');

        if (!is_string($webDavUrl) || $webDavUrl === '') {
            throw new RuntimeException(
                'NEXTCLOUD_WEBDAV_URL is missing.'
            );
        }

        if (!is_string($username) || $username === '') {
            throw new RuntimeException(
                'NEXTCLOUD_USERNAME is missing.'
            );
        }

        if (!is_string($appPassword) || $appPassword === '') {
            throw new RuntimeException(
                'NEXTCLOUD_APP_PASSWORD is missing.'
            );
        }

        $this->baseUrl = rtrim($webDavUrl, '/');
        $this->username = $username;
        $this->appPassword = $appPassword;
    }

    /**
     * @throws ConnectionException
     */
    public function uploadFile(string $folderPath, TemporaryUploadedFile $file): void
    {
        $filename = $file->getClientOriginalName();
        $remotePath = trim($folderPath, '/') . '/' . $filename;
        $response = Http::withBasicAuth(config('services.nextcloud.username'), config('services.nextcloud.app_password'))->timeout(120)->withBody(file_get_contents($file->getRealPath()), $file->getMimeType() ?? 'application/octet-stream')->put($this->buildUrl($remotePath));

        if (!$response->successful()) {
            throw new RuntimeException(
                "Nextcloud upload failed with error {$response->body()}"
            );
        }
    }

    public function createDirectory(string $path): void
    {
        $response = $this->request('MKCOL', $path);

        /*
         * 201: directory created
         * 405: directory already exists
         */
        if (!in_array($response->status(), [201, 405], true)) {
            throw new RuntimeException(
                "Could not create Nextcloud directory '{$path}'. "
                . "HTTP status: {$response->status()}. "
                . "Response: {$response->body()}"
            );
        }
    }

    public function deleteDirectory(string $path): void
    {
        $response = $this->request('DELETE', $path);

        /*
         * 204: deleted
         * 404: directory already does not exist
         */
        if (!in_array($response->status(), [204, 404], true)) {
            throw new RuntimeException(
                "Could not delete Nextcloud directory '{$path}'. "
                . "HTTP status: {$response->status()}. "
                . "Response: {$response->body()}"
            );
        }
    }

    private function request(string $method, string $path): Response
    {
        return Http::withBasicAuth(
            $this->username,
            $this->appPassword,
        )
            ->timeout(20)
            ->send($method, $this->buildUrl($path));
    }

    private function buildUrl(string $path): string
    {
        $segments = array_filter(
            explode('/', trim($path, '/')),
            fn(string $segment): bool => $segment !== '',
        );

        $encodedPath = implode(
            '/',
            array_map('rawurlencode', $segments),
        );

        if ($encodedPath === '') {
            return $this->baseUrl;
        }

        return $this->baseUrl . '/' . $encodedPath;
    }


}
