var $$ = Dom7;


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