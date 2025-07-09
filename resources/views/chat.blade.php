<!DOCTYPE html>
<html>
<head>
    <title>AI Assistant Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="w-full max-w-md bg-white rounded-xl shadow-xl overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 text-white px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="bg-white rounded-full p-1">
                <img src="https://cdn-icons-png.flaticon.com/512/4712/4712100.png" alt="Bot" class="h-8 w-8 rounded-full">
            </div>
            <div>
                <p class="font-semibold text-sm">Chat with TroikaBot</p>
                <p class="text-xs text-green-200">We're online</p>
            </div>
        </div>
        <div class="text-xl">⋮</div>
    </div>

    <!-- Chat Box -->
    <div id="chat-box" class="h-96 px-4 py-3 overflow-y-auto space-y-4 bg-gray-50"></div>

    <!-- Input -->
    <form id="chat-form" class="relative flex items-center border-t p-3 bg-white">
        <input type="text" id="question" class="flex-grow border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ask something..." required>
        <button type="submit" class="absolute right-5 text-blue-600 hover:text-blue-800 transition">
            <svg class="w-6 h-6 rotate-45" fill="currentColor" viewBox="0 0 24 24">
                <path d="M2.01 21L23 12 2.01 3v7l15 2-15 2z"/>
            </svg>
        </button>
    </form>
</div>

<script>
document.getElementById('chat-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const question = document.getElementById('question').value.trim();
    const chatBox = document.getElementById('chat-box');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


    if (!question) return;

    // Show user message
    chatBox.innerHTML += `
        <div class="text-right">
            <div class="inline-block bg-blue-500 text-white px-4 py-2 rounded-2xl text-sm max-w-[80%]">${question}</div>
        </div>
    `;
    chatBox.scrollTop = chatBox.scrollHeight;

    // ✅ Set API URL based on local or production
    const API_BASE = window.location.hostname.includes("127.0.0.1") || window.location.hostname.includes("localhost")
        ? "http://127.0.0.1:8000"
        : "https://laravel-chatbot-l1zw.onrender.com";

    try {
        const res = await fetch(`${API_BASE}/api/ask`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ question })
        });

        const data = await res.json();

        // Show bot reply
        chatBox.innerHTML += `
            <div class="text-left">
                <div class="inline-block bg-gray-200 text-gray-900 px-4 py-2 rounded-2xl text-sm max-w-[80%]">${data.answer}</div>
            </div>
        `;
    } catch (error) {
        console.error('Error:', error);
        chatBox.innerHTML += `
            <div class="text-left">
                <div class="inline-block bg-red-100 text-red-600 px-4 py-2 rounded-2xl text-sm max-w-[80%]">
                    Something went wrong. Please try again.
                </div>
            </div>
        `;
    }

    document.getElementById('question').value = '';
    chatBox.scrollTop = chatBox.scrollHeight;
});
</script>


</body>
</html>
