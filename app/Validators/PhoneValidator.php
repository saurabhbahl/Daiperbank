<?php namespace App\Validators;

class PhoneValidator {
	// validate a us phone number
	// any 10 digit or 11 digit number is acceptable
	// if 11 digits, the first digit must be 1
	public function validate($value, array $attributes = []) {
		$num = preg_replace('#\D+#i', '', $value);

		return
		strlen($num) == 10
			|| (strlen($num) == 11 && $num[0] == 1);
	}
}