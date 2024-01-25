var $$ = Dom7;
const server = "http://localhost:8000"

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

  on: {
    init: function () {
      getCSRFToken()
    }
  },

  pushState: true,
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


var view = app.views.create('.view-main', {
  browserHistory: true,
  on: {
    pageInit: function () {
      console.log('page init')
    }
  }
})

app.on('init', function () {
  // Call the function when the app is ready
  getCSRFToken();
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