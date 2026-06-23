<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountRequest extends Model
{
    protected $fillable = [
        'user_id', 'type', 'new_username', 'reason', 'status', 'admin_note',
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }
}
