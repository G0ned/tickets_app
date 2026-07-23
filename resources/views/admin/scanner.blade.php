<x-layout>
    @section('title', 'Escáner de entradas')
    <x-slot:heading>Escáner de entradas</x-slot:heading>

    <div class="max-w-sm mx-auto space-y-6">
        <p class="text-gray-600 text-center">Apunte la cámara al código QR de la entrada.</p>

        <div id="reader" class="w-full bg-black rounded-lg overflow-hidden"></div>

        <style>
            /* html5-qrcode genera estos elementos dinámicamente por JS,
               por lo que no se les puede aplicar clases de Tailwind directamente. */
            #reader__dashboard_section_csr,
            #reader__dashboard_section_csr span,
            #reader__dashboard_section_csr button,
            #reader__dashboard_section_swaplink,
            #reader__dashboard_section_fsr,
            #reader__dashboard_section_fsr span,
            #reader__dashboard_section_fsr button,
            #reader__status_span,
            #reader__header_message {
                color: #f9fafb !important;
            }
        </style>

        <div id="result" class="p-4 rounded-lg text-center hidden">
            <h2 id="status-title" class="text-xl font-bold text-white"></h2>
            <p id="status-msg" class="text-white"></p>
            <div id="not-accepted-rights" class="mt-3 text-sm text-gray-100"></div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        const html5QrcodeScanner = new Html5QrcodeScanner('reader', { fps: 10, qrbox: 250 });

        const onScanSuccess = (decodedText) => {
            html5QrcodeScanner.pause(true);

            fetch('{{ route('checkin-store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ token: decodedText }),
            })
                .then(response => response.json())
                .then(data => {
                    showResult(data.status, data.message, data.not_accepted_rights);
                    setTimeout(() => html5QrcodeScanner.resume(), 2000);
                })
                .catch(() => {
                    showResult('error', 'No se pudo contactar con el servidor.', []);
                    setTimeout(() => html5QrcodeScanner.resume(), 2000);
                });
        };

        function showResult(status, message, notAcceptedRights) {
            const resultDiv = document.getElementById('result');
            const title = document.getElementById('status-title');
            const msg = document.getElementById('status-msg');
            const rightsDiv = document.getElementById('not-accepted-rights');

            resultDiv.classList.remove('hidden', 'bg-green-600', 'bg-yellow-600', 'bg-red-600');

            if (status === 'success') {
                resultDiv.classList.add('bg-green-600');
                title.innerText = 'CORRECTO';
            } else if (status === 'warning') {
                resultDiv.classList.add('bg-yellow-600');
                title.innerText = 'ENTRADA YA ESCANEADA';
            } else if (status === 'early'){
                resultDiv.classList.add('bg-yellow-600');
                title.innerText = 'AVISO';
            } else if (status === 'late'){
                resultDiv.classList.add('bg-red-600');
                title.innerText = 'EDICIÓN PASADA';
            }
            else {
                resultDiv.classList.add('bg-red-600');
                title.innerText = 'INCORRECTO';
            }

            msg.innerText = message;

            rightsDiv.innerHTML = (notAcceptedRights && notAcceptedRights.length > 0)
                ? '<strong>Derechos revocados:</strong><br>' + notAcceptedRights.join('<br>')
                : '';
        }

        html5QrcodeScanner.render(onScanSuccess);
    </script>
</x-layout>
