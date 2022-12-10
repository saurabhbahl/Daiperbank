<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{

    protected $fillable = [
        'agency_id',
        'file'
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
