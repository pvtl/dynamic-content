<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DynamicContent extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'slug',
        'description',
    ];

    /** @return HasMany<DynamicContentSection, $this> */
    public function sections(): HasMany
    {
        return $this->hasMany(DynamicContentSection::class)->orderBy('order');
    }

    public static function getBySlug(string $slug): self
    {
        return self::firstOrCreate(
            ['slug' => $slug],
            ['description' => '']
        );
    }
}
