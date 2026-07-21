<?php

namespace App\Services;

use App\Models\Client;


class ClientChecklistService
{
    private const array DEFAULT_ITEMS = [
        'Initial consultation completed',
        'Client agreement signed',
        'Identity documents received',
        'Passport received',
        'Current visa checked',
        'Visa expiry date confirmed',
        'Police clearances requested',
        'Qualifications received',
        'Employment evidence received',
        'English test received',
        'Skills assessment checked',
        'Eligibility assessment completed',
        'Application prepared',
        'Client approval received',
        'Application submitted',
    ];

    public function createFor(Client $client): void
    {
        foreach (self::DEFAULT_ITEMS as $index => $title) {
            $client->checklistItems()->create(['title' => $title,
                'sort_order' => $index + 1,]);
        }
    }
}

;
