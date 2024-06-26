<?php namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller as BaseController;
use App\Inventory;
use App\InventoryAdjustment;
use App\ProductCategory;
use Illuminate\Http\Request;

class CreateController extends BaseController {

	public function get(Request $Request) {
		return view('admin.inventory.create', [
			'typeMap' => InventoryAdjustment::getTypeMap(),
			'ProductCategories' => ProductCategory::with('Product')->orderBy('name', 'ASC')->get(),
		]);
	}

	public function update($id) {

		$data = InventoryAdjustment::with('Inventory.Product')->where('id','=',$id)->get();

		return view('admin.inventory.update', [
			'typeMap' => InventoryAdjustment::getTypeMap(),
			'ProductCategories' => ProductCategory::with('Product')->orderBy('name', 'ASC')->get(),
			'Data' => $data
		]);
	}
	
	public function updatedata($id,Request $request) {
		$this->validate($request, $this->rules(), $this->messages());
		// Find the existing InventoryAdjustment record by its ID
		$adjustment = InventoryAdjustment::find($id);

		if (!$adjustment) {
			// Handle the case where the adjustment with the given ID doesn't exist
			return redirect()->back()->with('error', 'Inventory adjustment not found');
		}

		// Update the attributes of the existing adjustment with the new data
		$adjustment->adjustment_type = $request->get('type');
		$adjustment->adjustment_note = $request->get('note');
		$adjustment->adjustment_datetime = carbon($request->get('adjustment_datetime'));
	
		// Save the changes to the adjustment record
		$adjustment->save();
	
		 // Update the related inventory records (assuming they exist in the request)
		 if ($request->has('product')) {
			foreach ($request->get('product') as $product) {
				// Update or create the related inventory record
				$adjustment->inventory()->updateOrCreate(
					['product_id' => $product['product_id']],
					[
						'txn_type' => $request->get('debit_credit'),
						'amount' => $product['quantity'],
					]
				);
			}
		}
	
		return redirect()->route('admin.inventory.index')
        ->with('success', 'Inventory adjustment updated successfully');
	}

	public function post(Request $Request) {
		$this->validate($Request, $this->rules(), $this->messages());

		$Adjustment = InventoryAdjustment::create([
			'created_by_user_id' => auth()->user()->id,
			'adjustment_type' => $Request->get('type'),
			'adjustment_note' => $Request->get('note'),
			'adjustment_datetime' => carbon($Request->get('adjustment_datetime')),
		]);

		foreach ($Request->get('product') as $product) {
			$Adjustment->Inventory()->save(new Inventory([
				'txn_type' => $Request->get('debit_credit'),
				'product_id' => $product['product_id'],
				'amount' => $product['quantity'],
			]));
		}

		return redirect()->route('admin.inventory.index')
				->with('success', 'Inventory adjustment created successfully');
	}

	protected function rules() {
		return [
			'debit_credit' => ['required', 'in:debit,credit'],
			'type' => ['required', 'in:' . implode(',', array_keys(InventoryAdjustment::getTypeMap())), ],
			'adjustment_datetime' => ['required', 'date_format:Y-m-d\TH:i'],
			'note' => ['nullable', 'string'],
			'product' => ['array', 'min:1'],
			'product.*' => ['array', ],
			'product.*.product_id' => ['required', 'exists:product,id'],
			'product.*.quantity' => ['required', 'integer', 'min:1', ],
		];
	}

	protected function messages() {
		return [
			'debit_credit.required' => 'This field is required.',
			'debit_credit.in' => 'This field is required',

			'type.required' => 'This field is required.',
			'type.in' => 'This field is required.',

			'adjustment_datetime.required' => 'This field is required.',
			'adjustment_datetime.date_format' => 'This field is required.',

			'product.array' => 'You must specify at least one product',
			'product.min' => 'You must specify at least one product',

			'product.*.product_id.required' => 'This field is required',
			'product.*.product_id.exists' => 'This field is required',
			'product.*.quantity.required' => 'This field is required',
			'product.*.quantity.min' => 'This field is required',

		];
	}
}