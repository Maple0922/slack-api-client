<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kpi extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'member_notion_id',
        'content'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_notion_id', 'notion_id');
    }
}
