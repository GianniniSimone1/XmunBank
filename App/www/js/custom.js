

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
            if(data.count === 0)
                $('#contiCorrentiSwiper').html(`<swiper-slide> Non hai conti correnti</swiper-slide>`)
            else
            {
                $('#contiCorrentiSwiper').html('');
                $.each(data.owned, function (index, element) {
                    $('#contiCorrentiSwiper').append(`
                    <swiper-slide>
                        <div class="card card-outline bg-color-secondary owned" data-id="` + element.id + `">
                            <div class="card-header">` + element.iban + `</div>
                            <div class="card-content card-content-padding">Creato da: <i>` + element.owner_name + `</i></div>
                            <div class="card-footer">` + element.balance + `€</div>
                        </div>
                    </swiper-slide>
                `)
                });

                $.each(data.joined, function (index, element) {
                    $('#contiCorrentiSwiper').append(`
                    <swiper-slide>
                        <div class="card card-outline joined" data-id="` + element.id + `">
                            <div class="card-header">` + element.iban + `</div>
                            <div class="card-content card-content-padding">Creato da: <i>` + element.owner_name + `</i> - <b>Cointestato con te</b></div>
                            <div class="card-footer">` + element.balance + `€</div>
                        </div>
                    </swiper-slide>
                `)
                });
                $$('.joined, .owned').on('click', function() {
                    var contoId = $$(this).data('id');
                    app.views.main.router.navigate('/user/conto/' + contoId + '/');
                });
            }
        },
        error: function (error) {
            app.preloader.hide()
        }
    });


});

$(document).on('submit', '#newContoForm', function (e) {
    e.preventDefault();
    app.preloader.show();
    var cointestatariValue = $('[name="cointestatari"]').val().trim();
    if (cointestatariValue === '') {
        $('[name="cointestatari"]').val('nessuno');
    }
    var formData = $(this).serializeArray();
    $.post({
        url: server +  apiUrl + '/accounts/',
        data: formData,
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
        success: function (data) {
            app.preloader.hide();
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


$$(document).on('page:afterin', '.page[data-name="makeTransaction"]', function (e) {
    var page = e.detail;
    $('input[name="contoCorrenteId"]').val(page.route.params.contoId);
    console.log(page)
});


$(document).on('submit', '#newTransaction', function (e) {
    e.preventDefault();
    var formData = $(this).serializeArray();
    app.preloader.show();

    $.post({
        url: server +  apiUrl + '/transaction/make',
        data: formData,
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
        success: function (data) {
            app.preloader.hide();
            app.views.main.router.back('/user/conto/' + $('input[name="contoCorrenteId"]').val() + '/', {
                clearPreviousHistory: true
            });
        },
        error: function (error) {
            app.preloader.hide();
            app.dialog.alert(JSON.stringify(error.responseJSON.message));
        }
    });
});



$$(document).on('page:init', '.page[data-name="conto"]', function (e) {
    e.preventDefault();
    var page = e.detail;
    $('#newTransactionButton').attr('href', '/user/makeTransaction/' + page.route.params.contoId);
    app.preloader.show()
    $.get({
        url: server +  apiUrl + '/accounts/show',
        data: { contoCorrenteId: page.route.params.contoId },
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
        success: function (data) {
            app.preloader.hide()
            $$('.owner_name').html(data.result.owner_name)
            $$('.balance').html(data.result.balance)
            $$('.iban').html(data.result.iban)
            if(data.result.cointestatari.length === 0)
                $('.cointestatari').html(`Non ci sono cointestatari`)
            else
            $.each(data.result.cointestatari, function (index, element) {
                $('.cointestatari').append(`
                                    <div class="chip">
          <div class="chip-media bg-color-primary">
            <i class="icon f7-icons if-not-md">person</i>
            <i class="icon material-icons md-only">person</i>
          </div>
          <div class="chip-label">` + element.nome + ' ' + element.cognome + `</div>
        </div>
                `)
            });


            if(data.result.transactionsTo.length === 0)
                $('#tt').html(`Non ci sono transazioni`)
            else
                $.each(data.result.transactionsTo, function (index, element) {
                    $('#tt').append(`
                                    <tr>
                        <td class="label-cell">` + element.reason + `</td>
                        <td class="label-cell">` + element.fromDetails.owner_name + `</td>
                        <td class="numeric-cell text-color-green"> ` + element.value + `€</td>
                    </tr>
                `)
                });

            if(data.result.transactionsFrom.length === 0)
                $('#ft').html(`Non ci sono transazioni`)
            else
                $.each(data.result.transactionsFrom, function (index, element) {
                    $('#ft').append(`
                                    <tr>
                        <td class="label-cell">` + element.reason + `</td>
                        <td class="label-cell">` + element.toDetails.owner_name + `</td>
                        <td class="numeric-cell text-color-red"> ` + element.fee + `€</td>
                        <td class="numeric-cell text-color-red"> ` + element.value + `€</td>
                    </tr>
                `)
                });

        },
        error: function (error) {
            app.preloader.hide()
        }
    });
});
$$(document).on('page:reinit', '.page[data-name="conto"]', function (e) {
    e.preventDefault();
    $('.cointestatari').html('')
    $('#tt').html('')
    $('#ft').html('')
    var page = e.detail;
    $('#newTransactionButton').attr('href', '/user/makeTransaction/' + page.route.params.contoId);
    app.preloader.show()
    $.get({
        url: server +  apiUrl + '/accounts/show',
        data: { contoCorrenteId: page.route.params.contoId },
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
        success: function (data) {
            app.preloader.hide()
            $$('.owner_name').html(data.result.owner_name)
            $$('.balance').html(data.result.balance)
            $$('.iban').html(data.result.iban)
            if(data.result.cointestatari.length === 0)
                $('.cointestatari').html(`Non ci sono cointestatari`)
            else
            $.each(data.result.cointestatari, function (index, element) {
                $('.cointestatari').append(`
                                    <div class="chip">
          <div class="chip-media bg-color-primary">
            <i class="icon f7-icons if-not-md">person</i>
            <i class="icon material-icons md-only">person</i>
          </div>
          <div class="chip-label">` + element.nome + ' ' + element.cognome + `</div>
        </div>
                `)
            });


            if(data.result.transactionsTo.length === 0)
                $('#tt').html(`Non ci sono transazioni`)
            else
                $.each(data.result.transactionsTo, function (index, element) {
                    $('#tt').append(`
                                    <tr>
                        <td class="label-cell">` + element.reason + `</td>
                        <td class="label-cell">` + element.fromDetails.owner_name + `</td>
                        <td class="numeric-cell text-color-green"> ` + element.value + `€</td>
                    </tr>
                `)
                });

            if(data.result.transactionsFrom.length === 0)
                $('#ft').html(`Non ci sono transazioni`)
            else
                $.each(data.result.transactionsFrom, function (index, element) {
                    $('#ft').append(`
                                    <tr>
                        <td class="label-cell">` + element.reason + `</td>
                        <td class="label-cell">` + element.toDetails.owner_name + `</td>
                        <td class="numeric-cell text-color-red"> ` + element.fee + `€</td>
                        <td class="numeric-cell text-color-red"> ` + element.value + `€</td>
                    </tr>
                `)
                });

        },
        error: function (error) {
            app.preloader.hide()
        }
    });
});

function esci(){
    app.panel.close();
    app.preloader.show();
    $.post({
        url: server +  apiUrl + '/deleteToken',
        headers: {
            'Accept': 'application/json',// Aggiungi l'intestazione Accept
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
        success: function (data) {
            app.preloader.hide();
            localStorage.removeItem('token');
            app.views.main.router.navigate('/', {
                clearPreviousHistory: true
            });
        },
        error: function (error) {
            app.preloader.hide();
            app.dialog.alert(JSON.stringify(error.responseJSON.message));
        }
    });
}