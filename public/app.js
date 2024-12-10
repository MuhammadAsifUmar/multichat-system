const conn = new WebSocket('ws://localhost:8080/chat');

conn.onopen = () => console.log('Connected to server');
conn.onmessage = (e) => {
    const messages = document.getElementById('messages');
    messages.innerHTML += `<div>${e.data}</div>`;
};

document.getElementById('sendBtn').onclick = () => {
    const input = document.getElementById('messageInput');
    conn.send(input.value);
    input.value = '';
};
