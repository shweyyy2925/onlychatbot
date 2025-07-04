<!DOCTYPE html>
<html>
<head>
    <title>AI Chatbot</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
<div class="w-full max-w-md bg-white rounded-lg shadow-xl p-4">
    <div id="chat-box" class="h-96 overflow-y-auto mb-4 border p-2 rounded bg-gray-50"></div>
    <form id="chat-form" class="flex">
        <input type="text" id="question" class="flex-grow border rounded p-2 mr-2" placeholder="Ask a question..." required>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Send</button>
    </form>
</div>

<script>
document.getElementById('chat-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const question = document.getElementById('question').value;
    const chatBox = document.getElementById('chat-box');
    chatBox.innerHTML += `<div class="text-right text-green-600 mb-2"><strong>You:</strong> ${question}</div>`;

    try {
        const res = await fetch('/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ question })
        });

        if (!res.ok) {
            const errorText = await res.text();
            console.error("⚠️ Backend Error:", errorText);
            chatBox.innerHTML += `<div class="text-left text-red-600 mb-2"><strong>Bot:</strong> ${errorText}</div>`;
            return;
        }

        const data = await res.json();
        chatBox.innerHTML += `<div class="text-left text-purple-600 mb-2"><strong>Bot:</strong> ${data.answer}</div>`;
    } catch (error) {
        console.error("❌ Fetch error:", error);
        chatBox.innerHTML += `<div class="text-left text-red-600 mb-2"><strong>Bot:</strong> An unexpected error occurred.</div>`;
    }

    document.getElementById('question').value = '';
});
</script>

</body>
</html>
