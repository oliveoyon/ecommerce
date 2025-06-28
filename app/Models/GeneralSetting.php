<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $guarded = [];   // all columns fillable
    public static function current(): self
    {
        return cache()->rememberForever('general_settings', function () {
            return self::firstOrFail();
        });
    }
}
