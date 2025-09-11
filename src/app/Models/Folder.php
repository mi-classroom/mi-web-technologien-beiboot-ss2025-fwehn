<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'parent_folder_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parentFolder(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_folder_id');
    }

    public function childFolders(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_folder_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'folder_id');
    }

    public function iptc(): MorphOne
    {
        return $this->morphOne(IptcDataEntry::class, 'iptcable');
    }
}
