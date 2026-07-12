<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ClientFolderService
{
    protected array $folders = [
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

    public function create(Client $client): string
    {
        try {
            $folderName = "{$client->id}-" . Str::slug($client->first_name . ' ' . $client->last_name);
            $path = "clientsLMA/{$folderName}";
            
            // create main client folder
            Storage::disk('client_files')->makeDirectory($path);
            // create sub folders
            foreach ($this->folders as $folder) {
                Storage::disk('client_files')->makeDirectory("$path/$folder");
            }

            return $path;

        } catch (\Throwable $e) {
            dd($e->getMessage());
        }
    }
}
