<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Storage;

/**
 * App\Models\Scan
 */
class Scan extends Model
{
    use HasFactory;

    protected $hidden = [
        'id',
    ];

    protected $appends = ['file_url'];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function expenses()
    {
        return $this->belongsTo(Expense::class);
    }

    public function getFileUrl()
    {
        return route('admin.scans.get-file', $this->id);
    }

    public function getTemporaryUrl()
    {
        return Storage::disk('do')->temporaryUrl($this->file_path, Carbon::now()->addHour());
    }

    public function getFileExtension()
    {
        $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);

        return $extension;
    }

    public function getFileUrlAttribute()
    {
        return $this->getTemporaryUrl();
    }
}
