<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Module extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_key',
        'key',
        'name',
        'permission_title',
        'path',
        'icon',
        'status',
    ];

    public const STATUS = [
        '0'         => 'Inactive',
        '1'         => 'Active',
    ];

    public const STATUS_COLOR = [
        '0'         => 'badge text-bg-danger',
        '1'         => 'badge text-bg-primary',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (Module $module) {
            $module->key =  $module->key ?? strtolower(str_replace(' ', '_', $module->name));
        });
    }


    /**
     * Get the parent module.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'parent_key');
    }

    /**
     * Get the children modules.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Module::class, 'parent_key', 'key');
    }
}
