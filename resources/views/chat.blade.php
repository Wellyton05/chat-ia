<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Chat IA') }}</title>

    <style>
        :root {
            --bg: #0b0f14;
            --panel: #101824;
            --panel-2: #0e1a2b;
            --accent: #ff7a18;
            --accent-2: #7c4dff;
            --text: #e8eef6;
            --muted: #9bb0c7;
            --bubble-user: #1d6ef2;
            --bubble-bot: #1f2b3a;
            --input: #111a26;
            --border: #1e2a3a;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Space Grotesk", "Segoe UI", system-ui, sans-serif;
            color: var(--text);
            background: radial-gradient(1200px 600px at 10% -10%, rgba(124, 77, 255, 0.25), transparent),
                        radial-gradient(900px 500px at 90% 0%, rgba(255, 122, 24, 0.22), transparent),
                        var(--bg);
            min-height: 100vh;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            justify-content: center;
            padding: 32px 16px;
        }

        .shell {
            width: min(1080px, 100%);
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px;
        }

        .sidebar {
            background: linear-gradient(160deg, rgba(31, 43, 58, 0.9), rgba(16, 24, 36, 0.95));
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            font-weight: 700;
            color: #0b0f14;
            letter-spacing: 1px;
        }

        .brand h1 {
            font-size: 1.2rem;
            margin: 0;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.04);
            padding: 6px 10px;
            border-radius: 999px;
        }

        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .quick-actions button {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
            padding: 10px 12px;
            border-radius: 12px;
            text-align: left;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .quick-actions button:hover {
            border-color: rgba(255, 122, 24, 0.6);
            transform: translateY(-1px);
        }

        .panel {
            background: linear-gradient(160deg, rgba(16, 24, 36, 0.95), rgba(10, 15, 22, 0.95));
            border: 1px solid var(--border);
            border-radius: 18px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
            /* Added fixed height to force inner scrolling */
            height: calc(100vh - 64px);
            max-height: 800px;
        }

        .panel-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(13, 20, 30, 0.7);
            flex-shrink: 0;
        }

        .panel-header h2 {
            margin: 0;
            font-size: 1.1rem;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: var(--muted);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #45f08b;
            box-shadow: 0 0 10px #45f08b;
        }

        .messages {
            flex: 1;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            overflow-y: auto;
            /* Added min-height to ensure it shrinks instead of expanding parent */
            min-height: 0;
        }

        .message {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        .message.user {
            justify-content: flex-end;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: rgba(124, 77, 255, 0.2);
            display: grid;
            place-items: center;
            font-weight: 700;
            color: #b9a8ff;
        }

        .bubble {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 16px;
            line-height: 1.4;
            font-size: 0.96rem;
        }

        .bubble.bot {
            background: var(--bubble-bot);
            border: 1px solid rgba(124, 77, 255, 0.2);
        }

        .bubble.user {
            background: var(--bubble-user);
        }

        .meta {
            font-size: 0.75rem;
            color: var(--muted);
            margin-top: 6px;
        }

        .typing {
            display: inline-flex;
            gap: 4px;
        }

        .typing span {
            width: 6px;
            height: 6px;
            background: #9bb0c7;
            border-radius: 50%;
            animation: bounce 1s infinite ease-in-out;
        }

        .typing span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing span:nth-child(3) {
            animation-delay: 0.4s;
        }

        .composer {
            border-top: 1px solid var(--border);
            padding: 18px 24px;
            display: flex;
            gap: 12px;
            background: rgba(11, 17, 25, 0.9);
        }

        .composer input {
            flex: 1;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid transparent;
            background: var(--input);
            color: var(--text);
            font-size: 0.96rem;
        }

        .composer input:focus {
            outline: none;
            border-color: rgba(255, 122, 24, 0.8);
        }

        .composer button {
            padding: 12px 18px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            color: #0b0f14;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .composer button:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
        }

        .footer-note {
            text-align: center;
            color: var(--muted);
            font-size: 0.75rem;
            margin-top: 8px;
        }

        @keyframes bounce {
            0%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-6px); }
        }

        @media (max-width: 900px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .messages {
                height: 50vh;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="shell">
            <aside class="sidebar">
                <div class="brand">
                    <div class="brand-mark">IA</div>
                    <div>
                        <h1>Autoatendimento</h1>
                        <div class="badge">Chat inteligente</div>
                    </div>
                </div>

                <p style="margin: 0; color: var(--muted);">
                    Olá! Eu sou a Aurora, sua assistente virtual. Posso ajudar com planos, suporte e dúvidas gerais.
                </p>

                <div class="quick-actions">
                    <button type="button" data-quick="Quero saber os planos disponíveis.">Ver planos</button>
                    <button type="button" data-quick="Estou com problema no acesso.">Resolver acesso</button>
                    <button type="button" data-quick="Quero cancelar meu serviço.">Cancelar serviço</button>
                    <form method="POST" action="{{ route('logout') }}" style="margin-top: 1rem;">
                        @csrf
                        <button type="submit" style="width: 100%; border-color: rgba(255,100,100,0.5); color: #ff8a8a;">Sair (Logout)</button>
                    </form>
                </div>
            </aside>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>Chat com Aurora</h2>
                        <div class="status"><span class="status-dot"></span>Online agora</div>
                    </div>
                    <div class="badge">Tempo médio: 2 min</div>
                </div>

                <div class="messages" id="messages">
                    <div class="message">
                        <div class="avatar">A</div>
                        <div>
                            <div class="bubble bot">Oi! Sou sua assistente virtual. Como posso ajudar hoje?</div>
                            <div class="meta">Bot • Agora</div>
                        </div>
                    </div>
                </div>

                <form class="composer" id="composer">
                    <input
                        id="messageInput"
                        type="text"
                        placeholder="Digite sua mensagem..."
                        autocomplete="off"
                    />
                    <button type="submit">Enviar</button>
                </form>
            </section>
        </div>
    </div>

    <script>
        const userName = @json(auth()->user()->name ?? 'Você');
        const userInitial = userName.charAt(0).toUpperCase();

        const messagesEl = document.getElementById('messages');
        const composer = document.getElementById('composer');
        const messageInput = document.getElementById('messageInput');
        const quickButtons = document.querySelectorAll('[data-quick]');

        function appendMessage({ author, text, isUser }) {
            const wrapper = document.createElement('div');
            wrapper.className = `message ${isUser ? 'user' : ''}`;

            const avatar = document.createElement('div');
            avatar.className = 'avatar';
            avatar.textContent = isUser ? userInitial : 'A';

            const content = document.createElement('div');
            const bubble = document.createElement('div');
            bubble.className = `bubble ${isUser ? 'user' : 'bot'}`;
            bubble.textContent = text;

            const meta = document.createElement('div');
            meta.className = 'meta';
            meta.textContent = `${isUser ? userName : 'Bot'} • agora`;

            content.appendChild(bubble);
            content.appendChild(meta);

            if (isUser) {
                wrapper.appendChild(content);
            } else {
                wrapper.appendChild(avatar);
                wrapper.appendChild(content);
            }

            messagesEl.appendChild(wrapper);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function showTyping() {
            const wrapper = document.createElement('div');
            wrapper.className = 'message';
            wrapper.id = 'typing';

            const avatar = document.createElement('div');
            avatar.className = 'avatar';
            avatar.textContent = 'A';

            const content = document.createElement('div');
            const bubble = document.createElement('div');
            bubble.className = 'bubble bot';
            bubble.innerHTML = '<div class="typing"><span></span><span></span><span></span></div>';

            content.appendChild(bubble);
            wrapper.appendChild(avatar);
            wrapper.appendChild(content);

            messagesEl.appendChild(wrapper);
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }

        function hideTyping() {
            const typingEl = document.getElementById('typing');
            if (typingEl) typingEl.remove();
        }

        async function sendMessage(message) {
            if (!message.trim()) return;

            appendMessage({ author: userName, text: message, isUser: true });
            messageInput.value = '';
            showTyping();

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/chat/message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message })
                });

                const data = await response.json();
                hideTyping();
                appendMessage({ author: 'Bot', text: data.reply || 'Desculpe, não consegui responder agora.', isUser: false });
            } catch (error) {
                hideTyping();
                appendMessage({ author: 'Bot', text: 'Ops! Houve um problema ao enviar sua mensagem.', isUser: false });
            }
        }

        composer.addEventListener('submit', (event) => {
            event.preventDefault();
            sendMessage(messageInput.value);
        });

        quickButtons.forEach((button) => {
            button.addEventListener('click', () => {
                sendMessage(button.dataset.quick || '');
            });
        });
    </script>
</body>
</html>
