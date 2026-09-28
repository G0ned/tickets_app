<x-layout>
    @section('title', 'Escáner de entradas')
    <x-slot:heading>Escáner de entradas</x-slot:heading>

    {{-- min-h-[70dvh] (dvh, not vh - stable against the address bar showing/hiding
         on a phone) centers the scanner within the space available below the header.
         The result message lives in a grid row that's 0fr tall (collapsed) by
         default; showResult()/hideResult() toggle it to 1fr, which grows/shrinks
         it smoothly and, because it sits above #reader in a centered flex column,
         visibly pushes the scanner down and lets it re-center once it collapses
         again - no JS height math, no layout jump. --}}
    <div class="max-w-sm mx-auto min-h-[70dvh] flex flex-col items-center justify-center gap-6">
        <p class="text-gray-600 text-center">Apunte la cámara al código QR de la entrada.</p>

        <div id="result-row" class="w-full grid grid-rows-[0fr] transition-[grid-template-rows] duration-500 ease-in-out">
            <div class="overflow-hidden">
                <div id="result" class="p-4 rounded-lg text-center">
                    <h2 id="status-title" class="text-xl font-bold text-white"></h2>
                    <p id="status-msg" class="text-white"></p>
                    <div id="not-accepted-rights" class="mt-3 text-sm text-gray-100"></div>
                </div>
            </div>
        </div>

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
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        const html5QrcodeScanner = new Html5QrcodeScanner('reader', { fps: 10, qrbox: 250 });

        let isProcessing = false;

        function safePause() {
            try {
                html5QrcodeScanner.pause(true);
            } catch (e) {
                /* already paused (state race in html5-qrcode) */
            }
        }

        function safeResume() {
            try {
                html5QrcodeScanner.resume();
            } catch (e) {
                /* already scanning (state race in html5-qrcode) */
            }
        }

        const onScanSuccess = (decodedText) => {
            if (isProcessing) return;
            isProcessing = true;
            safePause();

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
                    setTimeout(() => {
                        isProcessing = false;
                        safeResume();
                    }, 2000);
                })
                .catch(() => {
                    showResult('error', 'No se pudo contactar con el servidor.', []);
                    setTimeout(() => {
                        isProcessing = false;
                        safeResume();
                    }, 2000);
                });
        };

        // How long the result stays pushed above the scanner before it
        // collapses back and the scanner re-centers.
        const RESULT_VISIBLE_MS = 5000;
        let hideResultTimeout = null;

        function showResult(status, message, notAcceptedRights) {
            const resultRow = document.getElementById('result-row');
            const resultDiv = document.getElementById('result');
            const title = document.getElementById('status-title');
            const msg = document.getElementById('status-msg');
            const rightsDiv = document.getElementById('not-accepted-rights');

            resultDiv.classList.remove('bg-green-600', 'bg-yellow-600', 'bg-red-600');

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

            resultRow.classList.remove('grid-rows-[0fr]');
            resultRow.classList.add('grid-rows-[1fr]');

            // Re-triggering mid-display (a new scan while the previous result is
            // still up) restarts the 5s window instead of hiding it early.
            clearTimeout(hideResultTimeout);
            hideResultTimeout = setTimeout(hideResult, RESULT_VISIBLE_MS);
        }

        function hideResult() {
            const resultRow = document.getElementById('result-row');
            resultRow.classList.remove('grid-rows-[1fr]');
            resultRow.classList.add('grid-rows-[0fr]');
        }

        html5QrcodeScanner.render(onScanSuccess);
    </script>
</x-layout>
