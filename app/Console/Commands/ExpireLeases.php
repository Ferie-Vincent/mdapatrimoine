<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Lease;
use App\Models\Property;
use App\Services\AuditService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpireLeases extends Command
{
    protected $signature = 'leases:expire';

    protected $description = 'Expire active leases whose end_date has passed and set their properties to disponible';

    public function handle(): int
    {
        $leases = Lease::where('status', 'actif')
            ->whereNotNull('end_date')
            ->where('end_date', '<', now()->toDateString())
            ->get();

        $count = 0;

        foreach ($leases as $lease) {
            DB::transaction(function () use ($lease) {
                $lease->update(['status' => 'expire']);

                Property::where('id', $lease->property_id)->update(['status' => 'disponible']);

                AuditService::log(
                    'expired',
                    $lease,
                    ['previous_status' => 'actif', 'end_date' => $lease->end_date]
                );
            });

            $count++;
        }

        $message = "Expired {$count} lease(s) past their end date.";
        Log::info($message);
        $this->info($message);

        return self::SUCCESS;
    }
}
