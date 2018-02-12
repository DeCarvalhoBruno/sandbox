const Login = () => import('~/pages/admin/auth/login').then(m => m.default || m)
const PasswordReset = () => import('~/pages/admin/auth/password/reset').then(
  m => m.default || m)
const PasswordRequest = () => import('~/pages/admin/auth/password/email').then(
  m => m.default || m)

const Dashboard = () => import('~/pages/admin/dashboard').then(
  m => m.default || m)
const Users = () => import('~/pages/admin/users/index').then(m => m.default || m)
const Settings = () => import('~/pages/admin/settings/index').then(
  m => m.default || m)
const SettingsProfile = () => import('~/pages/admin/settings/profile').then(
  m => m.default || m)
const SettingsPassword = () => import('~/pages/admin/settings/password').then(
  m => m.default || m)

export default [
  {
    path: '/admin',
    redirect: {name: 'admin.dashboard'},
  },
  {
    path: '/admin/dashboard',
    name: 'admin.dashboard',
    component: Dashboard
  },
  {
    path: '/admin/users',
    name: 'admin.user.index',
    component: Users
  },
  {
    path: '/admin/settings',
    component: Settings,
    children: [
      {
        path: '',
        redirect: {name: 'admin.settings.profile'}
      },
      {
        path: 'profile',
        name: 'admin.settings.profile',
        component: SettingsProfile
      },
      {
        path: 'password',
        name: 'admin.settings.password',
        component: SettingsPassword
      }
    ]
  },
  {
    path: '/admin/login',
    name: 'admin.login',
    component: Login
  },
  {
    path: '/admin/password/reset',
    name: 'admin.password.request',
    component: PasswordRequest
  },
  {
    path: '/admin/password/reset/:token',
    name: 'admin.password.reset',
    component: PasswordReset
  },
  {
    path: '*',
    component: require('~/pages/errors/404.vue')
  }
]
