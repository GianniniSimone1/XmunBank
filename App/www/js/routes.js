var routes = [
  {
    path: '/',
    url: './index.html',
  },
  {
    path: '/login/',
    url: './pages/login.html',
    name: 'login',
  },
  {
    path: '/home/',
    url: './pages/home.html',
    name: 'home',
  },
  {
    path: '/register/',
    url: './pages/register.html',
    name: 'register',
  },
  {
    path: '/user/',
    url: './pages/user/dashboard.html',
    name: 'dashboard',
  },
  {
    path: '/user/newConto',
    url: './pages/user/newContoCorrente.html',
    name: 'newConto',
  },
  {
    path: '/user/conto/:contoId',
    url: './pages/user/conto.html',
    name: 'newConto',
  },
  {
    path: '/user/makeTransaction/:contoId',
    url: './pages/user/makeTransaction.html',
    name: 'makeTransaction',
  }
];
