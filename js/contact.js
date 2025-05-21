document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Récupération des valeurs du formulaire
            const formData = {
                nom: document.getElementById('nom').value,
                prenom: document.getElementById('prenom').value,
                email: document.getElementById('email').value,
                telephone: document.getElementById('telephone').value,
                sujet: document.getElementById('sujet').value,
                message: document.getElementById('message').value
            };

            // Validation simple des champs
            if (!validateForm(formData)) {
                return;
            }

            // Simulation d'envoi du formulaire
            submitForm(formData);
        });
    }

    // Fonction de validation
    function validateForm(data) {
        // Validation de l'email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(data.email)) {
            showError('Veuillez entrer une adresse email valide');
            return false;
        }

        // Validation du téléphone (format gabonais)
        const phoneRegex = /^(\+241|0)[1-9]\d{7}$/;
        if (!phoneRegex.test(data.telephone)) {
            showError('Veuillez entrer un numéro de téléphone valide (+241 ou 0 suivi de 8 chiffres)');
            return false;
        }

        // Validation de la longueur du message
        if (data.message.length < 10) {
            showError('Votre message doit contenir au moins 10 caractères');
            return false;
        }

        return true;
    }

    // Fonction d'affichage des erreurs
    function showError(message) {
        // Création d'une alerte stylisée
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-error';
        alertDiv.textContent = message;

        // Insertion de l'alerte avant le formulaire
        contactForm.insertBefore(alertDiv, contactForm.firstChild);

        // Suppression de l'alerte après 5 secondes
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Fonction de succès
    function showSuccess(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success';
        alertDiv.textContent = message;

        contactForm.insertBefore(alertDiv, contactForm.firstChild);

        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Fonction de soumission du formulaire
    function submitForm(formData) {
        // Simulation d'une requête API
        // Dans un environnement réel, remplacer par un véritable appel API
        setTimeout(() => {
            showSuccess('Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.');
            contactForm.reset();
        }, 1000);
    }

    // Ajout des styles pour les alertes
    const style = document.createElement('style');
    style.textContent = `
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: var(--border-radius);
            animation: slideIn 0.3s ease;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background-color: #dcfce7;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
}); 