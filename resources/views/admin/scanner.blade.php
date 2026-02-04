<!DOCTYPE html>
<html>
<head>
    <title>Escáner QR</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white h-screen flex flex-col items-center justify-center">

    <div class="text-center mb-4">
        <h1 class="text-2xl font-bold">Escáner de entradas</h1>
        <p class="text-gray-400">Apunte la cámara al código QR</p>
    </div>

    <div id="reader" class="w-full max-w-sm bg-black rounded-lg overflow-hidden"></div>

    <div id="result" class="mt-6 p-4 rounded-lg text-center hidden w-full max-w-sm">
        <h2 id="status-title" class="text-xl font-bold"></h2>
        <p id="status-msg"></p>
        <div id="not-accepted-rights" class="mt-3 text-sm text-gray-200"></div>
    </div>

    <script>
        const onScanSuccess = (decodedText, decodedResult) => {
            try {
                html5QrcodeScanner.pause(true); 
            } catch (err) {
                console.log("Escáner en pausa. Inicializando...");
            }
            try {
                const data = JSON.parse(decodedText);
                fetch('/events/checkin', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        user_id: data.at_id,
                        event_id: data.event_id,
                    })
                })
                .then(response => response.json())
                .then(response => {
                    showResult(response.status, response.message, response.not_accepted_rights);
                    setTimeout(() => html5QrcodeScanner.resume(), 2000);
                });

            } catch (e) {
                console.error("Invalid QR JSON", e);
                html5QrcodeScanner.resume();
            }
        };

        function showResult(status, message, notAcceptedRights) {
            const resultDiv = document.getElementById('result');
            const title = document.getElementById('status-title');
            const msg = document.getElementById('status-msg');
            const rightsDiv = document.getElementById('not-accepted-rights');
            resultDiv.classList.remove('hidden', 'bg-green-500', 'bg-red-500', 'bg-yellow-500');
            if(status === 'success') {
                resultDiv.classList.add('bg-green-600');
                title.innerText = "CORRECTO";
            } else if (status === 'warning') {
                resultDiv.classList.add('bg-yellow-600');
                title.innerText = "ENTRADA YA ESCANEADA";
            } else {
                resultDiv.classList.add('bg-red-600');
                title.innerText = "INCORRECTO";
            }
            msg.innerText = message;
            
            if (notAcceptedRights && notAcceptedRights.length > 0) {
                rightsDiv.innerHTML = '<strong>Derechos revocados:</strong><br>' + notAcceptedRights.join('<br>');
            } else {
                rightsDiv.innerHTML = '';
            }
        }
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: 250 }
        );
        
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>