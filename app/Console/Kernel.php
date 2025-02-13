<?php

namespace App\Console;

use App\Models\Client;
use App\Models\Transaction;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {

            $transactions = Transaction::where('is_scheduled', true)
                ->where('next_scheduled_at', '<=', now())
                ->get();

            foreach ($transactions as $transaction) {
                $senderClient = Client::find($transaction->senderClient);
                $receiverClient = Client::find($transaction->client_id);

                if ($senderClient && $receiverClient) {
                    if ($senderClient->solde >= $transaction->amount) {
                        DB::transaction(function () use ($transaction, $senderClient, $receiverClient) {
                            $senderClient->decrement('solde', $transaction->amount);
                            $receiverClient->increment('solde', $transaction->amount);

                            $transaction->update([
                                'status' => 'validé',
                                'next_scheduled_at' => match ($transaction->frequency) {
                                    'day' => now()->addDay(),
                                    'week' => now()->addWeek(),
                                    'month' => now()->addMonth(),
                                    'minute' => now()->addMinute(),
                                },
                            ]);
                        });
                    } else {
                        $transaction->update(['status' => 'échec']);
                    }
                } else {
                    // Si un client n'est pas trouvé, mettre à jour la transaction avec un statut d'échec
                    $transaction->update(['status' => 'client non trouvé']);
                }
            }
        })->everyFiveSeconds(); 
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
