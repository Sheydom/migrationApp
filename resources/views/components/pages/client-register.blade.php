<?php

use App\Models\Client;
use Livewire\Component;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Filament\Forms\Components\FileUpload;
use App\Services\NextcloudService;
use Livewire\WithFileUploads;
use App\Jobs\PassportProcessingJob;

new class extends Component {
    use WithFileUploads;

    public function render()
    {
        return $this->view()->layout('layouts::client');
    }

    public string $success = "";
    public string $first_name;
    public string $last_name;
    public string $email;
    public string $phone;
    public string $nationality;
    public string $email_confirmation;
    public string $signedUrl;
    public $passport;
    public $current_visa;
    public $other_documents;

    public function mount(): void
    {
        $this->signedUrl = request()->fullUrl();
    }

    public function save(NextcloudService $nextcloud): void
    {
        $signedRequest = Request::create($this->signedUrl);
        if (!URL::hasValidSignature($signedRequest)) {
            abort(403, 'This registration link has expired or is invalid.');
        }


        $validated = $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email|confirmed',
            'phone' => 'required|string|max:30',
            'nationality' => 'required|string|max:50',
            'passport' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'current_visa' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'other_documents' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $client = Client::create(['first_name' => $validated['first_name'], 'last_name' => $validated['last_name'], 'email' => $validated['email'], 'nationality' => $validated['nationality']]);
        $client->refresh();
        try {
            if ($this->passport) {
                $nextcloud->uploadFile(folderPath: "{$client->folder_path}/Passport", file: $this->passport);
            }
            if ($this->current_visa) {
                $nextcloud->uploadFile(folderPath: "{$client->folder_path}/Visa", file: $this->current_visa);
            }
            foreach ($this->other_documents as $document) {
                $nextcloud->uploadFile(folderPath: "{$client->folder_path}", file: $document);
            }
        } catch (\Throwable $exception) {
            Log::error('Client document upload failed', [
                'client_id' => $client->id,
                'message' => $exception->getMessage(),
            ]);
        }

        //for PassportProcessing job we keep one local file temporary
        $localPath = $this->passport->storeAs('temp', "passport-{$client->id}.pdf");
        $fullPath = storage_path("app/private/{$localPath}");

        PassportProcessingJob::dispatch($client->id, $fullPath);

        $this->success = "Form successful submitted.";

//        $this->redirect('pages/thank-you');
    }

};
?>

<div class="min-h-screen bg-slate-100 py-12 px-4">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">

        <!-- Header -->
        <div class="bg-linear-to-r from-sky-900 to-cyan-700 px-8 py-10">
            {{--            Solis Migration --}}
            <h1 class="text-4xl font-bold text-white">
                Client intake Form
            </h1>

            {{--            <p class="text-blue-100 mt-3 text-lg">--}}
            {{--                Client Intake Form--}}
            {{--            </p>--}}

            <p class="text-blue-200 mt-2 text-sm">
                Please complete the form below. Your information will help us
                assess your migration options and prepare your consultation.
            </p>
        </div>

        <!-- Form -->
        <form wire:submit="save" enctype="multipart/form-data" class="p-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label for="first_name"
                           class="block text-sm font-semibold text-gray-700 mb-2">
                        First Name
                    </label>

                    <input
                        id="first_name"
                        type="text"
                        wire:model="first_name"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-cyan-600 focus:ring-2 focus:ring-cyan-200 outline-none">

                    @error('first_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name"
                           class="block text-sm font-semibold text-gray-700 mb-2">
                        Last Name
                    </label>

                    <input
                        id="last_name"
                        type="text"
                        wire:model="last_name"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-cyan-600 focus:ring-2 focus:ring-cyan-200 outline-none">

                    @error('last_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nationality"
                           class="block text-sm font-semibold text-gray-700 mb-2">
                        Nationality
                    </label>

                    <input
                        id="nationality"
                        type="text"
                        wire:model="nationality"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-cyan-600 focus:ring-2 focus:ring-cyan-200 outline-none">

                    @error('nationality')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone"
                           class="block text-sm font-semibold text-gray-700 mb-2">
                        Phone Number
                    </label>

                    <input
                        id="phone"
                        type="tel"
                        wire:model="phone"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-cyan-600 focus:ring-2 focus:ring-cyan-200 outline-none">

                    @error('phone')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email"
                           class="block text-sm font-semibold text-gray-700 mb-2">
                        Email Address
                    </label>

                    <input
                        id="email"
                        type="email"
                        wire:model="email"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-cyan-600 focus:ring-2 focus:ring-cyan-200 outline-none">

                    @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email_confirmation"
                           class="block text-sm font-semibold text-gray-700 mb-2">
                        Confirm Email Address
                    </label>

                    <input
                        id="email_confirmation"
                        type="email"
                        wire:model="email_confirmation"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-cyan-600 focus:ring-2 focus:ring-cyan-200 outline-none">

                    @error('email_confirmation')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-col cursor-pointer"><label class="cursor-pointer" for="passport">Passport</label>
                    <input type="file" id="passport" wire:model="passport" accept=".pdf,.jpg,.jpeg,.png"
                           class="inline-flex items-center px-4 py-2 bg-cyan-700 text-white rounded-lg cursor-pointer hover:bg-cyan-800 transition">

                    @error('passport') <p>{{$message}}</p> @enderror</div>
                <div class="flex flex-col cursor-pointer"><label class="cursor-pointer" for="currentVisa">Current
                        Visa</label>
                    <input type="file" id="currentVisa" wire:model="current_visa"
                           accept=".pdf,.jpg,.jpeg,.png"
                           class=" inline-flex items-center px-4 py-2 bg-cyan-700 text-white rounded-lg
                           cursor-pointer hover:bg-cyan-800 transition">

                    @error('current_visa') <p>{{$message}}</p> @enderror</div>

            </div>


            <div class="bg-cyan-50 border border-cyan-200 rounded-lg p-5">
                <h3 class="font-semibold text-cyan-800">
                    Privacy Notice
                </h3>

                <p class="text-sm text-gray-600 mt-2">
                    The information you provide will only be used by CompanyName to assess your migration enquiry and
                    communicate
                    with you regarding your application.
                </p>
            </div>


            <div class="flex justify-between items-center pt-2 w-full">
                <div class="flex-start">
                    @if($success)
                        <p class="text-green-500 text-2xl">{{$success}}</p>
                    @endif
                </div>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="bg-cyan-700 hover:bg-cyan-800 text-white font-semibold px-8 py-3 rounded-lg shadow transition">

                    <span wire:loading.remove>
                        Submit Application
                    </span>

                    <span wire:loading>
                        Submitting...
                    </span>


                </button>

            </div>


        </form>

    </div>
</div>
