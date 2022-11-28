<?php namespace App\Providers;

use App\Child;
use App\Guardian;
use App\Validators\PhoneValidator;
use App\Zip;
use DB;
use Illuminate\Support\ServiceProvider;
use Validator;

class CustomValidationServiceProvider extends ServiceProvider {
	public function boot() {
		$this->buildPhoneValidator();
		$this->buildFullNameValidator();
		$this->buildZipValidator();
		$this->buildOrderChildIdValidator();
		$this->buildOrderChildUniqueUniqIdValidator();
		$this->buildPickupDateMonthSelectorValidator();
		$this->buildUniqueChildIdentifierValidator();
		$this->buildUniqueFamilyIdentifierValidator();
	}

	protected function buildPhoneValidator() {
		Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
			return (new PhoneValidator)->validate($value, $parameters);
		});
	}

	protected function buildFullNameValidator() {
		Validator::extend('full_name', function($attribute, $value, $parameters, $validator) {
			return preg_match('#^[a-z]+\s+[a-z]+(\s+[a-z]+)?#i', $value);
		});
	}

	protected function buildZipValidator() {
		Validator::extend('zip', function($attribute, $value) {
			return Zip::find($value);
		});
	}

	protected function buildOrderChildIdValidator() {
		Validator::extend('agency_child_id', function($attribute, $value, $parameters, $validator) {
			$attr_parts = explode('.', $attribute);
			$attr_parts[ count($attr_parts) - 1 ] = 'isNew';
			$isNewAttr = implode('.', $attr_parts);

			if (array_get($validator->getData(), $isNewAttr)) {
				return true;
			}

			if (!count($parameters)) {
				return false;
			}

			return in_array($value, $parameters);
		});
	}

	protected function buildOrderChildUniqueUniqIdValidator() {
		Validator::extend('agency_child_unique_value', function($attribute, $value, $parameters, $validator) {
			$attr_parts = explode('.', $attribute);
			$attr_parts[ count($attr_parts) - 1 ] = 'id';
			$idAttribute = implode('.', $attr_parts);

			$child_id = array_get($validator->getData(), $idAttribute);
			list($unique_field, $agency_id) = $parameters;

			$Query = DB::table('child')
						->where($unique_field, $value)
						->where('agency_id', $agency_id);

			if ($child_id) {
				$Query->where('id', '!=', $child_id);
			}

			return $Query->count() == 0;
		});
	}

	protected function buildPickupDateMonthSelectorValidator() {
		Validator::extend('pickup_date_month_selector', function($attribute, $value) {
			if (!preg_match('#\d{4}-\d{2}#', $value)) return false;

			[ $year, $month ] = explode('-', $value);

			if ($year < 2017) return false;

			if ( ! ($month > 0 && $month < 13)) return false;

			return true;
		});
	}

	protected function buildUniqueChildIdentifierValidator() {
		Validator::extend('unique_child_identifier', function($attribute, $value, $parameters) {
			return ! Child::where('name', $value)
						->where('id', '!=', $parameters[0])
						->where('agency_id', $parameters[1])
						->first();
		});
	}

	protected function buildUniqueFamilyIdentifierValidator() {
		Validator::extend('unique_family_identifier', function($attribute, $value, $parameters) {
			return ! Guardian::where('name', $value)
						->where('id', '!=', $parameters[0])
						->where('agency_id', $parameters[1])
						->first();
		});
	}
}