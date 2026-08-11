<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Process;

use Illuminate\Http\Request;
use App\Models\Client;

class PythonController extends Controller
{
    /**
     * Show the form for creating the resource.
     */
    public function create()
    {
        set_time_limit(120);
        $script = base_path('app/Python/main.py');
        $python = base_path('app/Python/.venv/bin/python');
        $result = Process::timeout(120)->run([$python, $script]);


        //    $output = $result->output();
        $output = json_decode($result->output(), true);
        $client = Client::findOrFail(207);
        $client->update(['passport_number' => $output['passport_number']]);
        return view('python.create', ['output' => $output]);
    }

    /**
     * Store the newly created resource in storage.
     */
    public function store(Request $request): never
    {
        abort(404);
    }

    /**
     * Display the resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy(): never
    {
        abort(404);
    }
}
