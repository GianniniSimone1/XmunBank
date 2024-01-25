

$(document).on('submit', '#registrationForm', function (e) {
    e.preventDefault();
    // Aggiungi qui la logica di gestione della sottomissione del form
    var formData = $(this).serializeArray();

    app.preloader.show();
    $.post({
        url: server +  apiUrl + '/register',
        data: formData,
        headers: {
            'Accept': 'application/json' // Aggiungi l'intestazione Accept
        },
        success: function (data) {
            app.preloader.hide();
            localStorage.setItem('token', data.token);
            app.views.main.router.navigate('/user/', {
                clearPreviousHistory: true
            });
        },
        error: function (error) {
            app.preloader.hide();
            app.dialog.alert(JSON.stringify(error.responseJSON.message));
        }
    });
});

$(document).on('submit', '#loginForm', function (e) {
    e.preventDefault();
    // Aggiungi qui la logica di gestione della sottomissione del form
    var formData = $(this).serializeArray();

    app.preloader.show();
    $.post({
        url: server +  apiUrl + '/login',
        data: formData,
        headers: {
            'Accept': 'application/json' // Aggiungi l'intestazione Accept
        },
        success: function (data) {
            app.preloader.hide();
            localStorage.setItem('token', data.token);
            app.views.main.router.navigate('/user/', {
                clearPreviousHistory: true
            });
        },
        error: function (error) {
            app.preloader.hide();
            app.dialog.alert(JSON.stringify(error.responseJSON.message));
        }
    });
});

$$(document).on('page:init', '.page[data-name="dashboard"]', function (e) {
    app.preloader.show()
    $.get({
        url: server +  apiUrl + '/accounts/',
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
        success: function (data) {
            app.preloader.hide()
            $('#contiCorrentiSwiper').html('');
            $.each(data.owned, function(index, element) {
                $('#contiCorrentiSwiper').append(`
                    <swiper-slide>
                        <div class="card card-outline bg-color-secondary owned">
                            <div class="card-header">` + element.iban +`</div>
                            <div class="card-content card-content-padding">Creato da: ` + element.owner_name +`</div>
                            <div class="card-footer">` + element.balance +`€</div>
                        </div>
                    </swiper-slide>
                `)
            });

            $.each(data.joined, function(index, element) {
                $('#contiCorrentiSwiper').append(`
                    <swiper-slide>
                        <div class="card card-outline joined">
                            <div class="card-header">` + element.iban +`</div>
                            <div class="card-content card-content-padding">Creato da: ` + element.owner_name +` - Cointestato con te</div>
                            <div class="card-footer">` + element.balance +`€</div>
                        </div>
                    </swiper-slide>
                `)
            });
        },
        error: function (error) {
            app.preloader.hide()
        }
    });
});