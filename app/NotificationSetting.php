<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
   
    protected $table = "notification_settings";

    protected $fillable = [
        'notification_information'
    ];
}