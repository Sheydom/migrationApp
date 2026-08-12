<?php

namespace App\Jobs;

use App\Models\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Process;

class PassportProcessingJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected int $id, protected string $fullPath)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        set_time_limit(120);
        $script = base_path('app/Python/main.py');
        $python = base_path('app/Python/.venv/bin/python');
        $result = Process::timeout(120)->run([$python, $script, $this->fullPath]);


        //    $output = $result->output();
        $output = json_decode($result->output(), true);
        $client = Client::findOrFail($this->id);
        $client->update([
            'passport_number' => $output['passport_number'] ?? null,
            'expire_date' => $output['expire_date'] ?? null,
            'birth_date' => $output['birth_date'] ?? null,
            'gender' => ($output['gender'] ?? null) === 'F' ? 'Female' : 'Male',
            'status' => 'Review AI',
        ]);

        //remove temporary local passport file
        unlink($this->fullPath);
    }


}
