<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PdfAsset extends Model {
	protected $connection = null;
	protected $table = 'pdf_asset';
	public $timestamps = true;
	protected $guarded = [];

	use SoftDeletes;

	const TYPE_MANIFEST = 'manifest';
	const TYPE_LABELS = 'labels';

	public function pdfable() {
		return $this->morphTo('pdfable');
	}

	public function friendlyName() {
		switch ($this->asset_type) {
			case static::TYPE_MANIFEST:
				return "Manifest.pdf";
				break;

			case static::TYPE_LABELS:
				return "Labels.pdf";
				break;

			default:
				throw new Exception("Unknown asset type '{$this->asset_type}'.");
				break;
		}
	}

	static public function outputFilename($Target, $type) {
		switch ($type) {
			case static::TYPE_MANIFEST:
				$fn = static::outputManifestFilename($Target);
				break;

			case static::TYPE_LABELS:
				$fn = static::outputLabelFilename($Target);
				break;

			default:
				throw new Exception("Asset type '{$type}' not recognized.");
				break;
		}

		$fn = static::prefixPath($fn);
		static::createParentFolder($fn);

		return $fn;
	}

	static public function prefixPath($relative_path) {
		$sub_path = Str::finish(trim(config('hsdb.storage_folder', ''), '/'), '/');
		$relative_path = trim($relative_path, '/');

		return (strlen($sub_path) > 1)
					? $sub_path . $relative_path
					: $relative_path;
	}

	static public function createParentFolder($full_fn) {
		$dirname = dirname($full_fn);

		if (!file_exists(storage_path($dirname))) {
			mkdir(storage_path($dirname), 0755, true);
		}
	}

	static public function outputManifestFilename($Target) {
		return trim($Target->generateManifestFilename(), '/');

	}

	static public function outputLabelFilename($Target) {
		return trim($Target->generatePackingLabelFilename(), '/');
	}
}