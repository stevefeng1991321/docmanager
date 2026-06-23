<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id', 'display_name', 'avatar', 'view_mode', 'items_per_page',
        'notify_file_uploaded', 'notify_version_updated',
        'notify_access_denied', 'notify_doc_approved', 'notify_account_activated',
    ];

    protected function casts(): array
    {
        return [
            'notify_file_uploaded'     => 'boolean',
            'notify_version_updated'   => 'boolean',
            'notify_access_denied'     => 'boolean',
            'notify_doc_approved'      => 'boolean',
            'notify_account_activated' => 'boolean',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
}
