<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'words';
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'kurdish',
        'arabic',
        'description',
        'barcode',
        'is_saved',
        'is_favorite',
        'exported_at',
        'date_field',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_saved' => 'boolean',
        'is_favorite' => 'boolean',
        'exported_at' => 'datetime',
    ];
}
