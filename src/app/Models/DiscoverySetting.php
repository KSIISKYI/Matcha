<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscoverySetting extends Model
{
    protected $table = 'discovery_settings';

    protected $guarded = ['id'];
    public $timestamps = false;

    public function interests()
    {
        return $this->belongsToMany(Interest::class);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }
}
