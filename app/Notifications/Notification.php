<?php namespace App\Notifications;

use Illuminate\Notifications\Notification as BaseNotification;

class Notification extends BaseNotification {
	static public function getView() {
		return null;
	}

	static public function getViewData($Notification, $data) {
		return [];
	}

    static public function render($isPreview, $data, DatabaseNotification $Notification) {
    	$view = static::getView();

    	if ($view) {
	        return view(static::getView(), array_merge(static::getViewData($Notification, $data), [
	        	'isPreview' => $isPreview,
	        	'Notification' => $Notification,
	            'data' => $data,
	        ]));
	    }
    }
}