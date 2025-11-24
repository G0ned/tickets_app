<div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl mt-10">
    <div class="md:flex">
        <div class="p-8">
            <div class="uppercase tracking-wide text-sm text-indigo-500 font-semibold">
                Event Ticket
            </div>
            <a href="#" class="block mt-1 text-lg leading-tight font-medium text-black hover:underline">
                {{ $event->name }}
            </a>

            <p class="mt-2 text-gray-500">
                {{ $event->date->format('F j, Y, g:i a') }}
            </p>

            <div class="mt-4 border-t pt-4">
                <p class="text-gray-700 font-bold">Attendee:{{ $attendee->user?->firstname }}</p>
            </div>

            <div class="mt-6 flex justify-center">
                {{ QrCode::size(200)->generate($qrPayload) }}
            </div>

            <div clas="mt-4 text-center text-xs text-gray-400">
                Scan this code at the entrance
            </div>
        </div>
    </div>
</div>