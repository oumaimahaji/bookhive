<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot BookHive</title>
</head>
<body>
    <h1>Assistant BookHive 📚</h1>

    <form id="chatForm">
        @csrf
        <input type="text" id="userMessage" name="message" placeholder="Écrivez votre question..." required maxlength="500" />
        <button type="submit">Envoyer</button>
    </form>

    <div id="response" style="margin-top: 20px; font-weight: bold;"></div>

    <script>
    document.getElementById("chatForm").addEventListener("submit", async (e) => {
        e.preventDefault();

        const messageInput = document.getElementById("userMessage");
        const message = messageInput.value.trim();

        // Client-side validation
        if (!message) {
            document.getElementById("response").innerText = "Veuillez saisir un message.";
            messageInput.focus();
            return;
        }

        const formData = new FormData(e.target);

        try {
            const res = await fetch("/chatbot", {
                method: "POST",
                body: formData,
                headers: {
                    "Accept": "application/json"
                }
            });

            if (!res.ok) {
                let errorMessage = `Erreur HTTP ${res.status}`;
                if (res.status === 422) {
                    errorMessage = "Message invalide. Vérifiez que votre message n'est pas vide et ne dépasse pas 500 caractères.";
                }
                throw new Error(errorMessage);
            }

            const data = await res.json();
            document.getElementById("response").innerText = data.response;

        } catch (error) {
            console.error('Chat error:', error);
            document.getElementById("response").innerText = "Désolé, une erreur est survenue. Veuillez réessayer.";
        }

        // Réinitialiser le champ
        document.getElementById("userMessage").value = "";
    });
    </script>
</body>
</html>

