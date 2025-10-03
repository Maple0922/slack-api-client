<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevelopPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_notion_id',
        'in_review_date',
        'point',
        'target',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'in_review_date' => 'date',
        'timestamp' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_notion_id', 'notion_id');
    }
}
