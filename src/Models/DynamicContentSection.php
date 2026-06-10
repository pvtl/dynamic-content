<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DynamicContentSection extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'dynamic_content_id',
        'slug',
        'content',
        'order',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'content' => 'array',
            'order' => 'integer',
        ];
    }

    /** @return BelongsTo<DynamicContent, $this> */
    public function dynamicContent(): BelongsTo
    {
        return $this->belongsTo(DynamicContent::class);
    }
}
