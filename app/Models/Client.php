<?php

namespace App\Models;

use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'occupation',
        'nationality',
        'passport_number',
        'country_of_residence',
        'phone',
        'email',
        'current_visa',
        'expire_date',
        'status',
        'notes',
        'folder_path',

    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'expire_date' => 'date',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class);
    }
}
