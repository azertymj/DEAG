document.addEventListener('DOMContentLoaded', function() {
    // Création des éléments du chatbot
    const chatHTML = `
        <div class="chat-bubble">
            <i class="fas fa-comments"></i>
        </div>
        <div class="chat-window">
            <div class="chat-header">
                <h3>Service Client DEAG</h3>
                <button class="close-chat">&times;</button>
            </div>
            <div class="chat-messages">
                <div class="message bot">
                    Bonjour ! Comment puis-je vous aider aujourd'hui ?
                </div>
            </div>
            <div class="chat-input">
                <input type="text" placeholder="Écrivez votre message...">
                <button type="submit">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    `;

    // Ajout du chatbot au body
    const chatContainer = document.createElement('div');
    chatContainer.innerHTML = chatHTML;
    document.body.appendChild(chatContainer);

    // Sélection des éléments
    const chatBubble = document.querySelector('.chat-bubble');
    const chatWindow = document.querySelector('.chat-window');
    const closeChat = document.querySelector('.close-chat');
    const chatInput = document.querySelector('.chat-input input');
    const chatSubmit = document.querySelector('.chat-input button');
    const chatMessages = document.querySelector('.chat-messages');

    // Gestion des événements
    chatBubble.addEventListener('click', () => {
        chatWindow.classList.add('active');
        chatBubble.style.display = 'none';
    });

    closeChat.addEventListener('click', () => {
        chatWindow.classList.remove('active');
        chatBubble.style.display = 'flex';
    });

    function addMessage(message, isUser = false) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message');
        messageDiv.classList.add(isUser ? 'user' : 'bot');
        messageDiv.textContent = message;
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function handleUserMessage(message) {
        addMessage(message, true);
        
        // Simulation d'une réponse du bot
        setTimeout(() => {
            let response = "Je suis désolé, je suis en cours de développement. Veuillez nous contacter par téléphone ou email.";
            addMessage(response);
        }, 1000);
    }

    chatSubmit.addEventListener('click', () => {
        const message = chatInput.value.trim();
        if (message) {
            handleUserMessage(message);
            chatInput.value = '';
        }
    });

    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            const message = chatInput.value.trim();
            if (message) {
                handleUserMessage(message);
                chatInput.value = '';
            }
        }
    });
}); 