<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Google Fonts: Plus Jakarta Sans, Outfit & Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @stack('styles')

</head>

<body>

    @include('layouts.sidebar')

    <div class="main-wrapper">

        @include('layouts.navbar')

        <div class="content">

            @yield('content')

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @stack('scripts')
    @yield('scripts')

    <!-- Twemoji JS -->
    <script src="https://cdn.jsdelivr.net/npm/twemoji@14.0.2/dist/twemoji.min.js" crossorigin="anonymous"></script>
    <style>
        img.emoji {
            height: 1.2em;
            width: 1.2em;
            margin: 0 .05em 0 .1em;
            vertical-align: -0.1em;
            border-radius: 2px;
        }
    </style>
    <!-- Floating AI Assistant Button -->
    <div id="aiFloatingBtnContainer" style="position: fixed; bottom: 25px; right: 25px; z-index: 9999;">
        <button type="button" id="toggleAiChatBtn" class="btn btn-primary rounded-circle shadow-lg p-0 d-flex align-items-center justify-content-center position-relative hover-lift" style="width: 58px; height: 58px;">
            <i class="bi bi-robot fs-3 text-white"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 9px; padding: 3px 6px;">AI LIVE</span>
        </button>
    </div>

    <!-- AI Chatbot Modal Drawer -->
    <div id="aiChatDrawer" class="shadow-lg border-0 rounded-4 bg-white overflow-hidden d-none position-fixed" style="position: fixed; bottom: 95px; right: 25px; width: 395px; max-width: 92vw; height: 530px; z-index: 9999; display: flex; flex-direction: column;">
        <!-- Header -->
        <div class="p-3 bg-primary text-white d-flex justify-content-between align-items-center shadow-xs">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-white text-primary p-2 d-flex align-items-center justify-content-center shadow-xs" style="width: 34px; height: 34px;">
                    <i class="bi bi-robot fs-5"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">GlobalRisk AI Assistant</h6>
                    <small style="font-size: 11px; opacity: 0.9;">Neural Risk & Logistics Intelligence</small>
                </div>
            </div>
            <button type="button" id="closeAiChatBtn" class="btn-close btn-close-white" aria-label="Close"></button>
        </div>

        <!-- Chat Messages Body -->
        <div id="aiChatMessages" class="p-3 flex-grow-1 overflow-auto bg-light" style="font-size: 13px; line-height: 1.5;">
            <div class="d-flex mb-3">
                <div class="p-3 rounded-3 bg-white border shadow-xs text-dark" style="max-width: 92%;">
                    👋 <strong>Halo! Saya GlobalRisk AI Assistant.</strong><br><br>
                    Saya dapat membantu Anda menganalisis:<br>
                    • Waktu & rute pengiriman maritim maritim<br>
                    • Evaluasi risiko negara & pelabuhan<br>
                    • Volatilitas nilai tukar mata uang
                </div>
            </div>
        </div>

        <!-- Sample Quick Prompts Bar (Scrollable without ugly scrollbar) -->
        <div class="px-2 py-2 border-top bg-white d-flex gap-1.5 align-items-center" style="overflow-x: auto; white-space: nowrap; scrollbar-width: none; -ms-overflow-style: none;">
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-2.5 fw-medium" style="font-size: 11px; flex-shrink: 0;" onclick="sendAiSamplePrompt('Pengiriman dari Indonesia ke China')">🇮🇩 Indonesia ➔ 🇨🇳 China</button>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-2.5 fw-medium" style="font-size: 11px; flex-shrink: 0;" onclick="sendAiSamplePrompt('Rute Shanghai ke Rotterdam')">🚢 Shanghai ➔ Rotterdam</button>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-2.5 fw-medium" style="font-size: 11px; flex-shrink: 0;" onclick="sendAiSamplePrompt('Negara mana High Risk?')">⚠️ High Risk</button>
        </div>

        <!-- Input Control Footer -->
        <div class="p-2.5 border-top bg-white d-flex gap-2 align-items-center">
            <input type="text" id="aiChatInput" class="form-control form-control-sm rounded-pill px-3 py-2" placeholder="Ketik pertanyaan AI..." onkeypress="if(event.key==='Enter') sendAiMessage()">
            <button type="button" onclick="sendAiMessage()" class="btn btn-primary rounded-circle p-0 d-flex align-items-center justify-content-center shadow-xs" style="width: 38px; height: 38px; flex-shrink: 0;">
                <i class="bi bi-send-fill text-white" style="font-size: 13px;"></i>
            </button>
        </div>
    </div>

    <!-- AI Chatbot Scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggleBtn = document.getElementById('toggleAiChatBtn');
            const closeBtn = document.getElementById('closeAiChatBtn');
            const drawer = document.getElementById('aiChatDrawer');

            if (toggleBtn && drawer) {
                toggleBtn.addEventListener('click', function () {
                    drawer.classList.toggle('d-none');
                });
            }

            if (closeBtn && drawer) {
                closeBtn.addEventListener('click', function () {
                    drawer.classList.add('d-none');
                });
            }

            function parseMarkdownText(str) {
                if (!str) return '';
                let s = str;
                // Replace markdown bold **text** with HTML <strong>text</strong>
                s = s.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                // Replace markdown italic *text* with HTML <em>text</em>
                s = s.replace(/\*(.*?)\*/g, '<em>$1</em>');
                // Replace newlines with <br>
                s = s.replace(/\n/g, '<br>');
                return s;
            }

            window.sendAiSamplePrompt = function (text) {
                const input = document.getElementById('aiChatInput');
                if (input) {
                    input.value = text;
                    sendAiMessage();
                }
            };

            window.sendAiMessage = function () {
                const input = document.getElementById('aiChatInput');
                const messagesContainer = document.getElementById('aiChatMessages');
                const query = input.value.trim();

                if (!query) return;

                // Append User Message
                const userDiv = document.createElement('div');
                userDiv.className = 'd-flex justify-content-end mb-3';
                userDiv.innerHTML = `<div class="p-2.5 rounded-3 bg-primary text-white shadow-xs fw-medium" style="max-width: 85%;">${query}</div>`;
                messagesContainer.appendChild(userDiv);

                input.value = '';
                messagesContainer.scrollTop = messagesContainer.scrollHeight;

                // Loading Indicator
                const loadingDiv = document.createElement('div');
                loadingDiv.className = 'd-flex mb-3';
                loadingDiv.id = 'aiLoadingDiv';
                loadingDiv.innerHTML = `<div class="p-2.5 rounded-3 bg-white border shadow-xs text-muted"><i class="bi bi-arrow-repeat spin me-1 text-primary"></i> AI sedang menganalisis data...</div>`;
                messagesContainer.appendChild(loadingDiv);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;

                // Fetch Response
                fetch('/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: query })
                })
                .then(res => res.json())
                .then(data => {
                    const loadEl = document.getElementById('aiLoadingDiv');
                    if (loadEl) loadEl.remove();

                    const botDiv = document.createElement('div');
                    botDiv.className = 'd-flex mb-3';
                    let formattedResp = parseMarkdownText(data.response || 'Terjadi kesalahan sistem AI.');
                    botDiv.innerHTML = `<div class="p-3 rounded-3 bg-white border shadow-xs text-dark" style="max-width: 92%;">${formattedResp}</div>`;
                    messagesContainer.appendChild(botDiv);
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                })
                .catch(err => {
                    const loadEl = document.getElementById('aiLoadingDiv');
                    if (loadEl) loadEl.remove();
                });
            };
        });
    </script>
</body>

</html>