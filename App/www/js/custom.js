const apiUrl = '/api/v1'


$(document).on('submit', '#loginForm', function (e) {
    e.preventDefault();
    // Aggiungi qui la logica di gestione della sottomissione del form
    var formData = $(this).serializeArray();
    console.log(formData);

    app.preloader.show();
    $.post({
        url: server +  apiUrl + '/login',
        data: formData,
        success: function (data) {
            app.preloader.hide();
            localStorage.setItem('token', data);
        },
        error: function (error) {
            app.preloader.hide();
            app.dialog.alert(JSON.stringify(error.responseJSON.message));
        }
    });
});
