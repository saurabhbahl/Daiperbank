<?php namespace App\Http\Controllers\Api\Child;

use App\Child;
use App\ChildData;
use App\Guardian;
use App\Http\Controllers\Controller as BaseController;
use DB;
use Exception;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Log;

class ViewController extends BaseController {
	public function deny() {
		return response()->json([
			'success' => false,
			'message' => 'You are not authorized to do that.',
		], 403);
	}

	public function get(Request $Request, Child $Child) {
		if (Gate::denies('view', $Child)) {
			return $this->deny();
		}

		$Child->load(['Guardian', 'Sibling', 'NewestData', 'Location', 'OrderItem', 'OrderItem.Order', 'OrderItem.Product']);

		return response()->json([
			'success' => true,
			'data' => [
				'Child' => $Child->toArray(),
				'BenefitSummary' => $Child->getBenefitSummary(),
			],
		]);
	}

	public function post(Request $Request, Child $Child) {
		if ($Child->exists) {
			if (Gate::denies('update', $Child)) {
				return $this->deny();
			}
		} else {
			if (Gate::denies('create', Child::class)) {
				return $this->deny();
			}
		}

		try {
			// dd($Request);
			$this->validate($Request, $this->rules($Child), $this->messages());

			$saved = DB::transaction(function () use ($Request, $Child) {
				$new = false;
				if ( ! $Child->exists) {
					$Child->fill([
						'agency_id' => Auth()->User()->Agency->id,
					])->save();
					$new = true;
				}

				$child_data = Arr::only($Request->get('Child'), [
					'name',
					'gender',
					'dob',
					'zip',
					'race',
					'ethnicity',
					'guardian_id',
					'guardian_relationship',
					'is_menstruator',
				]);

				// had to add this line because we were getting this error on save:
				/**
	             * [2018-02-10 15:19:11] local.ERROR: InvalidArgumentException: Unexpected data found.
	             * Unexpected data found.
	             */
				// Some not so helpful details: https://stackoverflow.com/questions/34968061/laravel-carbon-data-missing
				// So, to fix: generate actual carbon object instead of relying on eloquent do to it for us.
				
				$child_data['dob'] = ! empty($child_data['dob']) ? carbon($child_data['dob']) : null;

				if ($Request->get('update_guardian', false)) {
					// are we updating the guardian's information (or creating a new one)?
					$postedGuardian = $Request->get('Guardian');

					if ( ! empty($postedGuardian['id'])) {
						$Guardian = Auth()->User()->Agency->Guardian()->find($postedGuardian['id']);

						if ( ! $Guardian) {
							throw new \Exception('Could not edit selected guardian.');
						}
					} else {
						$Guardian = new Guardian;
					}

					$Guardian->fill(Arr::only($postedGuardian, ['name', 'military_status']));
					Auth()->User()->Agency->Guardian()->save($Guardian);

					$child_data['guardian_id'] = $Guardian->id;
				}

				$saved = $Child->update($child_data);

				$CurrentData = $Child->NewestData ?: new ChildData(['child_id' => $Child->id]);
				$NewData = new ChildData(Arr::except($CurrentData->toArray(), [
					'id',
					'created_at',
					'updated_at',
					'updated_by_user_id',
				]));

				$meta_data = $Request->get('Child');

				$NewData->fill(Arr::only($meta_data, ['status_potty_train', 'status_wic']));
				$NewData->updated_by_user_id = Auth()->User()->id;

				$Child->Data()->save($NewData);

				return true;
			});

			if ( ! $saved) {
				throw new \Exception('Could not save child information.');
			}

			$Child->refresh();
			$Child->load(['Guardian', 'Sibling', 'NewestData', 'Location', 'OrderItem', 'OrderItem.Order', 'OrderItem.Product']);

			return response()->json([
				'success' => $saved,
				'data' => [
					'Child' => $Child->toArray(),
					'BenefitSummary' => $Child->getBenefitSummary(),
				],
			]);
		} catch (ValidationException $e) {
			Log::error("Child could not be saved", [ 'POST' => $Request->all(), 'Child' => $Child->toArray(), 'errors' => $e->validator->errors()->toArray() ]);

			return response()->json([
				'success' => false,
				'message' => 'Could not save child, an unexpected error occurred.',
				'data' => [
					'errors' => $e->validator->errors(),
				],
			], 422);
		} catch (Exception $e) {
			Log::error($e);
			return response()->json([
				'success' => false,
				'message' => 'An unexpected error occurred. ' . $e->getMessage(),
			], 500);
		}
	}

	public function delete(Request $Request, Child $Child) {
		if (Gate::denies('delete', $Child)) {
			return $this->deny();
		}

		$deleted = $Child->delete();
		return response()->json([
			'success' => $deleted,
		]);
	}

	protected function rules(Child $Child) {
		return [
			'Child.name' => ['required', 'string', 'min:3', 'unique_child_identifier:' . ($Child->exists ? $Child->id : null) . ',' . Auth()->User()->Agency->id, ],
			'Child.gender' => ['nullable', 'in:m,f'],
			'Child.zip' => ['nullable', 'numeric', 'regex:#^\d{5}(-\d{4})?$#', 'zip'],
			'Child.dob' => ['nullable', 'date'],
			'Child.race' => ['nullable', 'string'],
			'Child.ethnicity' => ['nullable', 'string'],
			'Child.status_potty_train' => ['boolean', 'nullable'],
			'Child.status_wic' => ['boolean', 'nullable'],
			'Child.guardian_id' => ['required_unless:update_guardian,true'],

			'Guardian.name' => ['required_if:update_guardian,true', 'string', 'unique_family_identifier:' . ($Child->guardian_id) . ',' . Auth()->User()->Agency->id, ],
			'Guardian.relationship' => ['nullable,true'],
			'Guardian.military_status' => ['nullable', 'string'],
		];
	}

	protected function messages() {
		return [
			'Child.name.required' => 'A child\'s unique identifier is required.',
			'Child.name.min' => "This child's unique identifier is not long enough.",
			'Child.name.unique_child_identifier' => 'This unique identifier has already been assigned to another child.',
			'Child.gender.required' => 'Child\'s gender is required.',
			'Child.gender.in' => 'Please select the child\'s gender from the options above.',
			'Child.zip.required' => "The child's zip code is required.",
			'Child.zip.numeric' => 'Please enter a valid zip.',
			'Child.zip.regex' => 'Please enter a valid zip.',
			'Child.zip.zip' => 'Please enter a valid zip.',
			'Child.dob.required' => 'Please enter a valid date.',
			'Child.dob.dob' => 'Please enter a valid date.',
			'Child.race.required' => 'This field is required',
			'Child.race.string' => 'This field is required',
			'Child.ethnicity.required' => 'This field is required',
			'Child.ethnicity.string' => 'This field is required',
			'Child.status_potty_train.required' => 'Potty training status is required.',
			'Child.status_wic.required' => 'WIC status is required.',
			'Child.guardian_id.required_unless' => 'You must select a parent/guardian for this child.',
			'Child.guardian_relationship.required' => 'Guardian relationship is required.',

			'Guardian.name.required_if' => 'This field is required',
			'Guardian.name.unique_family_identifier' => 'This unique family identifier has already been assigned to another family.',
			// 'Guardian.relationship.required_if' => 'This field is required',
			// 'Guardian.military_status.required_if' => 'This field is required.',
		];
	}

	protected function updateordercount(Request $request){
		$data = $request->all();
		$child = Child::find($data['child_id']);
		$child->order_count = $data['selectedProduct']['order_count'];
		$child->save();
		return response()->json([
			'success' => true,
			'order'	=> $data['selectedProduct']['order_count'],
		]);
	}

	// move child to archive
	public function unarchive(Request $request){
		$data = $request->all();
		$Child = Child::withTrashed()->find($data['cid']);

		$Child->load(['Guardian', 'Sibling', 'NewestData', 'Location', 'OrderItem', 'OrderItem.Order', 'OrderItem.Product']);
		
		return response()->json([
			'success' => true,
			'data' => [
				'Child' => $Child->toArray(),
				'BenefitSummary' => $Child->getBenefitSummary(),
			],
		]);
	}

	// update child archive to unarchive
	public function update(Request $request){
		$data = $request->all();
		// Retrieve the child record
		$child = Child::withTrashed()->find($data['cid']);
		$child->restore();

		$child->load(['Guardian', 'Sibling', 'NewestData', 'Location', 'OrderItem', 'OrderItem.Order', 'OrderItem.Product']);

		return response()->json([
			'success' => true,
			'data' => [
				'Child' => $child->toArray(),
				'BenefitSummary' => $child->getBenefitSummary(),
			],
		]);
	}
}
