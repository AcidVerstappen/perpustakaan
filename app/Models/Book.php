<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Book extends Model
{
    protected $fillable = [
        'kode_buku',
        'isbn',
        'judul',
        'slug',
        'category_id',
        'shelf_id',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'jumlah_buku',
        'stok_tersedia',
        'cover',
        'deskripsi',
    ];

    protected static function booted(): void
    {
        static::creating(function (Book $book) {
            if (empty($book->slug)) {
                $book->slug = static::generateUniqueSlug($book->judul);
            }
        });

        static::updating(function (Book $book) {
            if ($book->isDirty('judul') && ! $book->isDirty('slug')) {
                $book->slug = static::generateUniqueSlug($book->judul, $book->id);
            }
        });
    }

    public static function generateUniqueSlug(string $judul, ?int $ignoreId = null): string
    {
        $slug = Str::slug($judul);
        $original = $slug;
        $counter = 1;

        while (static::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function shelf(): BelongsTo
    {
        return $this->belongsTo(Shelf::class);
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover ? asset('storage/'.$this->cover) : null;
    }

    public function isAvailable(): bool
    {
        return $this->stok_tersedia > 0;
    }
}
