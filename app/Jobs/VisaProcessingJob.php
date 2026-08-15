<?php

namespace App\Jobs;
use Exception;
use App\Models\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class VisaProcessingJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected int $id, protected string $fullPathVisa)
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
            $script = base_path("app/Python/visa.py");
//            only for local
//            $python = base_path("app/Python/.venv/bin/python");
            $python = "/opt/venv/bin/python";
            $result = Process::timeout(120)->run([$python, $script, $this->fullPathVisa]);


            if ($result->failed()) {
                Log::error('Python visa processing failed', [
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

        $output = json_decode($result->output(), true);
        $client = Client::findOrFail($this->id);
        $client->update(['current_visa' => $output['current_visa'] ?? null]);
//        remove temporary local visa file
        unlink($this->fullPathVisa);


    }
}
