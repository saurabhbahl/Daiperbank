<?php namespace App\Notifications;

use Illuminate\Notifications\DatabaseNotificationCollection as BaseDbNotificationCollection;

class DatabaseNotificationCollection extends BaseDbNotificationCollection {
	public $hasMore = false;

	public function unread() {
		return $this->reduce(function($count, $Notification) {
			return $count + ($Notification->read_at === null? 1 : 0);
		}, 0);
	}

	public function limit($limit) {
		$Chunk = $this->chunk($limit)->first();

		if ($this->count() > $limit) {
			$Chunk->hasMore = true;
		}

		return $Chunk;
	}

	public function firstUnread() {
		$this->getUnread()->sortBy('created_at', SORT_REGULAR, true)->first();
	}

	public function lastUnread() {
		$this->getUnread()->sortBy('created_at')->first();
	}

	public function getUnread() {
		return $this->filter(function($Notification) {
			return ! $Notification->read_at;
		});
	}
}