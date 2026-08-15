<?php

namespace App\Jobs;

use Exception;
use App\Models\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;


class PassportProcessingJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected int $id, protected string $fullPathPassport)
    {
        //
    }

    /**
     * Execute the job.
     * @throws \Throwable
     */
    public function handle(): void
    {

        try {
            set_time_limit(120);
            $script = base_path('app/Python/passport.py');
            //            only for local
//            $python = base_path("app/Python/.venv/bin/python");
            $python = app()->environment('production') ? '/opt/venv/bin/python' : base_path("app/Python/.venv/bin/python");
            $result = Process::timeout(120)->run([$python, $script, $this->fullPathPassport]);


            if ($result->failed()) {
                Log::error('Python passport processing failed', [
                    'stdout' => $result->output(),
                    'stderr' => $result->errorOutput(),
                    'exit_code' => $result->exitCode(),
                ]);
                throw new Exception($result->errorOutput());
            }

        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            throw $e;
        }


        //    $output = $result->output();
        $output = json_decode($result->output(), true);
        $client = Client::findOrFail($this->id);
        $client->update([
            'passport_number' => $output['passport_number'] ?? null,
            'expire_date' => $output['expire_date'] ?? null,
            'birth_date' => $output['birth_date'] ?? null,
            'nationality' => $output['nationality'] ?? null,
            'gender' => ($output['gender'] ?? null) === 'F' ? 'Female' : 'Male',
            'status' => 'Review AI',
        ]);

        //remove temporary local passport file
        unlink($this->fullPathPassport);
    }


}
