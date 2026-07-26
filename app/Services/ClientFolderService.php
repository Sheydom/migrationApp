<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ClientFolderService
{
    private const array SUBFOLDERS = [
        'Visa',
        'Passport',
        'Police-Clearances',
        'Qualifications',
        'Employment',
        'English-Test',
        'Invoices',
        'Correspondence',
        'Grant',
        'Refusal',

    ];

    public function __construct(

        private readonly NextcloudService $nextcloud,

    )
    {

    }

    public function create(Client $client): string
    {
        try {
            $rootFolder = trim(config('nextcloud.root_folder', 'Clients'));
            $folderName = "{$client->id}-" . Str::slug($client->first_name . ' ' . $client->last_name);
            $clientPath = "{$rootFolder}/{$folderName}";

            // create main client folder
            $this->nextcloud->createDirectory($rootFolder);
            $this->nextcloud->createDirectory($clientPath);
            // create sub folders
            foreach (self::SUBFOLDERS as $folder) {
                $this->nextcloud->createDirectory("$clientPath/$folder");
            }

            return $clientPath;

        } catch (\Throwable $e) {
            dd($e->getMessage());
        }
    }

    public function delete(Client $client): void
    {
        try {
            if (!$client->folder_path) {
                return;
            }
            $this->nextcloud->deleteDirectory("$client->folder_path");
        } catch (\Throwable $e) {
            dd($e->getMessage());
        }
    }
}
