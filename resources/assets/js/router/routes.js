const Login = () => import('~/pages/admin/auth/login').then(m => m.default || m)
const PasswordReset = () => import('~/pages/admin/auth/password/reset').then(
  m => m.default || m)
const PasswordRequest = () => import('~/pages/admin/auth/password/email').then(
  m => m.default || m)

const Dashboard = () => import('~/pages/admin/dashboard').then(
  m => m.default || m)

const Users = () => import('~/pages/admin/users/index').then(
  m => m.default || m)
const UserEdit = () => import('~/pages/admin/users/edit').then(
  m => m.default || m)

const Groups = () => import('~/pages/admin/groups/index').then(
  m => m.default || m)
const GroupEdit = () => import('~/pages/admin/groups/edit').then(
  m => m.default || m)
const GroupAdd = () => import('~/pages/admin/groups/add').then(
  m => m.default || m)
const GroupMember = () => import('~/pages/admin/groups/member').then(
  m => m.default || m)

const Settings = () => import('~/pages/admin/settings/index').then(
  m => m.default || m)
const SettingsGeneral = () => import('~/pages/admin/settings/general').then(
  m => m.default || m)
const SettingsProfile = () => import('~/pages/admin/settings/profile').then(
  m => m.default || m)
const SettingsPassword = () => import('~/pages/admin/settings/password').then(
  m => m.default || m)

import store from '~/store'

let defaults = [
  {
    path: '/admin/',
    name: 'admins',
    redirect: {name: 'admin.dashboard'}
  },
  {
    path: '/admin/dashboard',
    name: 'admin.dashboard',
    component: Dashboard
  },
  {
    path: '/admin/users',
    name: 'admin.users.index',
    meta: {parent: 'admin.dashboard'},
    component: Users
  },
  {
    path: '/admin/users/:user',
    name: 'admin.users.edit',
    meta: {parent: 'admin.users.index'},
    component: UserEdit
  },
  {
    path: '/admin/users/:user/delete',
    name: 'admin.users.delete'
  },
  {
    path: '/admin/groups',
    name: 'admin.groups.index',
    meta: {parent: 'admin.dashboard'},
    component: Groups
  },
  {
    path: '/admin/groups/create',
    name: 'admin.groups.add',
    meta: {parent: 'admin.groups.index'},
    component: GroupAdd
  },
  {
    path: '/admin/groups/:group',
    name: 'admin.groups.edit',
    meta: {parent: 'admin.groups.index'},
    component: GroupEdit
  },
  {
    path: '/admin/groups/:group/members',
    name: 'admin.groups.members',
    meta: {parent: 'admin.groups.index'},
    component: GroupMember
  },
  {
    path: '/admin/settings',
    component: Settings,
    meta: {parent: 'admin.dashboard'},
    children: [
      {
        path: '/admin/settings',
        name: 'admin.settings',
        redirect: {name: 'admin.settings.profile'}
      },
      {
        path: '/admin/settings/general',
        name: 'admin.settings.general',
        component: SettingsGeneral
      },
      {
        path: '/admin/settings/profile',
        name: 'admin.settings.profile',
        component: SettingsProfile
      },
      {
        path: '/admin/settings/password',
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

async function loadLocalizedRouteUrls (locale) {
  return await import(`~/lang/routes-${locale}`)
}

(async function () {
  let locale = store.getters['lang/locale']
  let routes = await loadLocalizedRouteUrls(locale)
  let isDefaultLocale = (
    locale === store.getters['lang/fallback']
  )
  if (!isDefaultLocale) {
    for (var i in defaults) {
      if (routes.hasOwnProperty(defaults[i].name)) {
        defaults[i].path = '/' + locale + '/' + routes[defaults[i].name]
      }
    }
  }
})()
export default defaults
