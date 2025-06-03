<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OffDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_notion_id',
        'date'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_notion_id', 'notion_id');
    }
}
