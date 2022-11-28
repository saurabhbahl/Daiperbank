<?php namespace App;

use App\Notifications\DatabaseNotification;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Agency extends Model {
	protected $table = 'agency';
	protected $guarded = [];

	use Notifiable;

	const STATUS_ACTIVE = 'active';
	const STATUS_INACTIVE = 'inactive';

	protected $casts = [
		'flag_is_admin' => 'bool',
	];

	public function scopeVisible($query) {
		return $query->where('flag_visible', '=', 1);
	}

	public function User() {
		return $this->belongsTo(User::class, 'id', 'agency_id', 'User');
	}

	public function Order() {
		return $this->hasMany(Order::class, 'agency_id', 'id', 'Order');
	}

	public function OrderDraft() {
		return $this->Order()->where('order_status', Order::STATUS_DRAFT)->orderBy('created_at', 'ASC');
	}

	public function Contact() {
		return $this->belongsToMany(Contact::class, 'agency_contact', 'agency_id', 'contact_id', 'Contact');
	}

	public function Children() {
		return $this->hasMany(Child::class, 'agency_id', 'id', 'Children');
	}

	public function Guardian() {
		return $this->hasMany(Guardian::class, 'agency_id', 'id', 'Guardian');
	}

	public function Note() {
		return $this->morphMany(Note::class, 'notable', 'model', 'model_id')
					->orderBy('created_at', 'DESC');
	}

	public function notifications() {
		return $this->morphMany(DatabaseNotification::class, 'notifiable')
						->orderBy('created_at', 'desc');
	}

	public function isAdmin() {
		return true === $this->flag_is_admin;
	}

	public function isActive() {
		return $this->agency_status == static::STATUS_ACTIVE;
	}

	public function getFirstAvailablePickupDate() {
		return PickupDate::where('pickup_date', '>', DB::raw('NOW() + INTERVAL 89 HOUR'))
					->orderBy('pickup_date', 'ASC')
					->first();
	}

	public function newOrder() {
		return new Order([
			'agency_id' => $this->id,
			'created_by_user_id' => Auth()->User()->id,
		]);
	}

	public function routeNotificationForMail() {
		return $this->Contact->first()->email ?? null;
	}

	public function markNotificationsRead($since = null, $until = null) {
		$Query = $this->notifications()->whereNull('read_at');

		if ($since) {
			$since = Carbon::createFromFormat('U', $since ?? carbon()->format('U'))->format('Y-m-d H:i:s');
			$Query->where('created_at', '>=', $since);
		}

		if ($until) {
			$until = Carbon::createFromFormat('U', $until ?? carbon()->format('U'))->format('Y-m-d H:i:s');
			$Query->where('created_at', '<=', $until);
		}

		$Query->update([ 'read_at' => DB::raw('NOW()') ]);
	}

	public static function getAdminAgencies() {
		return static::where('flag_is_admin', 1)->get();
	}

	public static function getAllPartners() {
		return static::where('flag_is_admin', 0)->where('agency_status', '!=', static::STATUS_INACTIVE)->get();
	}
	public static function getStatuses() {
		$statuses = [
			static::STATUS_ACTIVE,
			static::STATUS_INACTIVE,		
		];

		$status_map = [];
		foreach ($statuses as $s) {
			$status_map[$s] = static::agencyStatusText($s);
		}

		return $status_map;
	}

	public static function agencyStatusText($status) {
		switch ($status) {
			case static::STATUS_INACTIVE:
				return 'Disable Partner';

			case static::STATUS_ACTIVE:
				return 'Active Partner';

			default:
				return 'Unknown Status';
		}
	}
}
