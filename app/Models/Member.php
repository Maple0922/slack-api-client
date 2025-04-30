<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'notion_id',
        'name',
        'image_url',
        'team_id',
        'target_point',
        'is_valid'
    ];

    protected $casts = [
        'is_valid' => 'boolean',
        'target_point' => 'integer'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function kpis()
    {
        return $this->hasMany(Kpi::class, 'member_notion_id', 'notion_id');
    }
}
