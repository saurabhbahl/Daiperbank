<?php

namespace App\Console\Commands;

use App\Fulfillment;
use App\PickupDate;
use Illuminate\Console\Command;

class GeneratePackingManifest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hsdb:generate-packing-manifest {--batch=} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export packing manifest for pickup dates or fulfillment batches';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('batch')) {
            $this->generateBatchManifest($this->option('batch'));
        }

        if ($this->option('date')) {
            $this->generatePickupDateManifest($this->option('date'));
        }
    }

    protected function generateBatchManifest($id) {
        $Batch = Fulfillment::findOrFail($id);

        return $Batch->generateManifest();
    }

    protected function generatePickupDateManifest($id) {
        $PickupDate = PickupDate::findOrFail($id);

        return $PickupDate->generateManifest();
    }
}
