<?php

namespace App\Observers;

use App\Models\Client;
use App\Services\ClientFolderService;


class ClientObserver
{
    /**
     * Handle the Client "created" event.
     */
    public function __construct(private readonly ClientFolderService $clientFolderService)
    {
    }

    public function created(Client $client): void
    {

        $folderPath = $this->clientFolderService->create($client);
        $client->updateQuietly(['folder_path' => $folderPath,]);
    }

    /**
     * Handle the Client "updated" event.
     */
    public function updated(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "deleted" event.
     */
    public function deleted(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "restored" event.
     */
    public function restored(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "force deleted" event.
     */
    public function forceDeleted(Client $client): void
    {
        //
    }
}
