// Aggiungi il gestore di eventi per il form di accesso
$(document).on('submit', '#loginForm', function (e) {
    e.preventDefault();
    // Aggiungi qui la logica di gestione della sottomissione del form
    var formData = $(this).serializeArray();
    alert('Well done');
    // Esegui le azioni necessarie, ad esempio invia una richiesta AJAX di accesso
});