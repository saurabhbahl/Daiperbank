<?php namespace App\Http\Controllers\Admin\Agency;

use App\Agency;
use App\Contact;
use App\Http\Controllers\Controller;
use App\User;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateController extends Controller {
	protected $Agency;

	protected function loadAgency($Agency) {
		if ($Agency && $Agency->exists) {
			$this->Agency = $Agency->load(['Contact', 'User', 'Note']);
		}
	}

	public function get(Agency $Agency = null) {
		$this->loadAgency($Agency);

		return view('admin.agency.update', [
			'Agency' => $this->Agency ?? new Agency,
			'editing' => true,
		]);
	}

	public function post(Request $Request, Agency $Agency = null) {
		$this->loadAgency($Agency);
		$is_creating = $Agency === null || ! $Agency->exists;
		$saved = false;

		try {
			$this->validate($Request, $this->rules($Agency), $this->messages());

			$data = $Request->all();

			$saved = DB::transaction(function () use ($data, $Agency, $is_creating) {
				$agency_data = array_get($data, 'agency');
				$agency_fields = ['name', 'address', 'address_2', 'city', 'state', 'zip'];

				// do not allow the agency prefix to be modified once it has been set.
				if ( ! $Agency || ! $Agency->id_prefix) {
					$agency_fields [] = 'id_prefix';
				}

				$agency_data = array_only($agency_data, $agency_fields);

				if ($is_creating) {
					$Agency = Agency::create($agency_data);
				} else {
					tap($Agency)->update($agency_data)->refresh();
				}

				$user_data = array_get($data, 'user');
				$user_fields = ['username'];

				if ($is_creating) {
					$user_fields[] = 'password';
				} else {
					if ( ! empty(array_get($user_data, 'password'))) {
						$user_fields[] = 'password';
					}
				}

				$user_data = array_only($user_data, $user_fields);
				$user_data = array_merge($user_data, [
					'agency_id' => $Agency->id,
					'name' => $Agency->name,
					'email' => array_get($data, 'contact.email'),
				]);

				if ($is_creating) {
					$User = User::create($user_data);
				} else {
					$User = tap($this->Agency->User)->update($user_data);
				}

				$contact_data = array_get($data, 'contact');
				$contact_fields = ['name', 'phone', 'phone_extension', 'email'];
				$contact_data = array_only($contact_data, $contact_fields);
				$Contact = $Agency->Contact()->first();

				if ($is_creating || ! $Contact) {
					$Contact = Contact::create($contact_data);
					$Agency->Contact()->sync([$Contact->id]);
				} else {
					tap($Contact)
						->update($contact_data);
				}

				return true;
			});
		} catch (ValidationException $e) {
			throw $e;
		} catch (Exception $e) {
			if (app()->bound('sentry')) {
				app('sentry')->captureException($e);
			}
		}

		if ($saved) {
			if ($is_creating) {
				flash('New agency, ' . e($Agency->name) . ' created succesfully!')->success();
			} else {
				flash('Agency successfully updated.')->success();
			}

			return redirect()->route('admin.agency.index');
		}

		if ($is_creating) {
			flash('Could not create new agency, an unexpected error occurred.')->error();
			return redirect()->route('admin.agency.create')->withInput();
		}

		flash('Could not update agency record, an unexpected error occurred.')->error();
		return redirect()->route('admin.agency.update')->withInput();
	}

	protected function rules(Agency $Agency = null) {
		$rules = [
			'agency.name' => ['required', 'min:1'],
			'agency.id_prefix' => [
				null === $Agency ? 'required' : null,
				'min:3',
				'max:15',
				Rule::unique('agency', 'id_prefix')->ignore($Agency->id ?? null),
			],
			'agency.address' => ['required', 'string', 'min:3', 'regex:#^.+\s+.+#'],
			'agency.address_2' => ['string', 'nullable'],
			'agency.city' => ['required', 'string'],
			'agency.state' => ['required', 'string', 'max:2'],
			'agency.zip' => ['required', 'string', 'max:10', 'regex:#^([0-9]{5}(-?[0-9]{4})?)?$#'],
			'agency.agency_status' => ['string', 'nullable', 'in:active,inactive'],
			'agency.flag_can_bulk_order' => ['numeric', 'nullable', 'between:0,1'],

			'user.username' => [
				'required',
				'string',
				'alpha_dash',
				'min:3',
				'max:25',
				Rule::unique('users', 'username')->ignore($Agency->User()->first()->id ?? null),
			],

			'user.password' => [
				(null === $Agency ? 'required' : 'nullable'),
				'string', 'min:6', 'same:user.confirm_password',
			],
			'user.confirm_password' => [
				(null === $Agency ? 'required' : 'nullable'),
				'required_with:user.password',
				'string', 'min:6', 'same:user.password',
			],

			'contact.name' => ['required', 'string', 'min:2', 'max:50'],
			'contact.phone' => ['required_without:contact.email', 'string', 'nullable', 'phone'],
			'contact.phone_extension' => ['numeric', 'nullable'],
			'contact.email' => ['required_without:contact.phone', 'email'],
		];

		if (null == $Agency) {
			array_unshift($rules['user.password'], 'required');
		}

		return $rules;
	}

	protected function messages() {
		return [
			'agency.name.required' => 'This field is required.',
			'agency.name.min' => 'This field is required.',
			'agency.name.min' => 'This field must be at least 1 characters.',
			'agency.id_prefix.required' => 'An agency identifier is required',
			'agency.id_prefix.min' => 'Agency identifier must be between 3 and 15 characters',
			'agency.id_prefix.max' => 'Agency identifier must be between 3 and 15 characters',
			'agency.address.required' => 'Agency address is required',
			'agency.address.string' => 'Agency address is required',
			'agency.address.min' => 'Agency address is required',
			'agency.address.regex' => 'Agency address is required',
			'agency.city.required' => 'A city is required',
			'agency.state' => 'Please select a state from the list',
			'agency.zip.required' => 'A valid US zip code is required.',
			'agency.zip.max' => 'Please enter a valid US zip code.',
			'agency.zip.regex' => 'Please enter a valid US zip code.',

			'user.username.required' => 'A login username is required.',
			'user.username.alpha_dash' => 'Usernames may be alpha numeric, and contain a hyphen (-).',
			'user.username.min' => 'Usernames must be between 3 and 25 characters.',
			'user.username.min' => 'Usernames must be between 3 and 25 characters.',
			'user.username.unique' => 'This username is already in use.',

			'user.password.required' => 'A password is required.',
			'user.password.string' => 'A password is required',
			'user.password.min' => 'Password must be at least 6 characters long',
			'user.password.same' => 'Password must match confirmation password',
			'user.password.required_with' => 'A password is required.',
			'user.password.same' => 'Passwords do not match.',

			'user.confirm_password.required' => 'Please re-type your password.',
			'user.confirm_password.string' => 'Please re-type your password.',
			'user.confirm_password.min' => '',
			'user.same' => 'Passwords do not match.',

			'contact.name.required' => 'An agency contact is required',
			'contact.name.min' => 'Please enter the agency contact\'s full name.',
			'contact.name.max' => 'Contact name too long, 50 characters maximum.',
			'contact.phone.required_without' => 'Phone number or email address is required.',
			'contact.phone.phone' => 'Please enter a valid US phone number.',
			'contact.email.required_without' => 'Email address or phone number is required.',
			'contact.email.email' => 'Please enter a valid e-mail address.',
		];
	}
}
