var $$ = Dom7;
const server = "http://localhost:8000"
const apiUrl = '/api/v1'


var app = new Framework7({
  name: 'Xmun Bank', // App name
  theme: 'auto', // Automatic theme detection
  colors: {
    primary: '#0B3954',
    secondary: '#4392F1',
    white: '#E7F0FF',
    light: '#E3EBFF',
    dark: '#010210',
    black: '#363635'
  },

  //pushState: true,
  darkMode: false,
  el: '#app', // App root element

  // App store
  store: store,
  // App routes
  routes: routes,
  // Register service worker
  serviceWorker: {
    path: '/service-worker.js',
  },
});

$$(document).on('page:beforein', '.page[data-name="index"]', function (e) {
  getCSRFToken();
  isValidToken();
});



var view = app.views.create('.view-main', {
  browserHistory: false,
  browserHistoryStoreHistory: false,
  browserHistoryTabs: 'push',
  on: {
    pageInit: function () {


      }
    }
})



function isValidToken(){
  if(localStorage.getItem('token'))
  $.post({
    url: server +  apiUrl + '/token',
    headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer ' + localStorage.getItem('token')
    },
    success: function (data) {
      app.views.main.router.navigate('/user/', {
        clearPreviousHistory: true
      });
      app.views.main.router.clearPreviousHistory()
    },
    error: function (error) {
      localStorage.removeItem('token')
      app.views.main.router.navigate('/home/', {
        clearPreviousHistory: true
      });
      app.views.main.router.clearPreviousHistory()
    }
  });
else {
    app.views.main.router.navigate('/home/', {
      clearPreviousHistory: true
    });
    app.views.main.router.clearPreviousHistory()
  }
}

$$(document).on('page:reinit', '.page[data-name="index"]', function (e) {
  getCSRFToken();
  isValidToken();
});


function getCSRFToken() {
  $.ajax({
    url: server + '/sanctum/csrf-cookie',
    type: 'GET',
    xhrFields: {
      withCredentials: false,
    },
    success: function (data) {
      console.log('Richiesta e token CSRF ottenuto con successo');
    },
    error: function (error) {
      console.error('Errore durante il recupero del token CSRF:', error);
    },
  });
}

