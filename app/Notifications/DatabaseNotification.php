<?php namespace App\Notifications;

use Illuminate\Notifications\DatabaseNotification as BaseDbNotification;

class DatabaseNotification extends BaseDbNotification {
    public function newCollection(array $models = []) {
        return new DatabaseNotificationCollection($models);
    }

	public function render($preview = true) {
		return call_user_func([$this->type, 'render'], $preview, $this->data, $this);
	}

	public function humanReadablePostedTime() {
		if (120 > $diff = carbon()->diffInSeconds($this->created_at)) {
			return 'a few seconds ago';
		} elseif (120 < $diff && $diff < 300) {
			return 'a few minutes ago';
		} elseif (carbon()->format('Y-m-d') == $this->created_at->format('Y-m-d')) {
			return $this->created_at->format('g:ia') . " today";
		} elseif (carbon('-1 day')->format('Y-m-d') == $this->created_at->format('Y-m-d')) {
			return $this->created_at->format('g:ia') . " yesterday";
		} else {
			return $this->created_at->format('M j, Y @ g:ia');
		}
	}
}