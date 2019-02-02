const Login = () => import('~/pages/admin/auth/login').then(m => m.default || m)

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

const Blog = () => import('~/pages/admin/blog/index').then(
  m => m.default || m)
const BlogAdd = () => import('~/pages/admin/blog/add').then(
  m => m.default || m)
const BlogCategory = () => import('~/pages/admin/blog/category').then(
  m => m.default || m)

const Settings = () => import('~/pages/admin/settings/index').then(
  m => m.default || m)
const SettingsGeneral = () => import('~/pages/admin/settings/general').then(
  m => m.default || m)
const SettingsProfile = () => import('~/pages/admin/settings/profile').then(
  m => m.default || m)
const SettingsPassword = () => import('~/pages/admin/settings/password').then(
  m => m.default || m)

const MediaEdit = () => import('~/pages/admin/media/edit').then(
  m => m.default || m)

import routesI18n from '~/lang/routes'
import store from '~/store'

let routes = [
  {
    name: 'admins',
    redirect: {name: 'admin.dashboard'}
  },
  {
    name: 'admin.dashboard',
    component: Dashboard
  },
  {
    name: 'admin.users.index',
    meta: {parent: 'admin.dashboard'},
    component: Users
  },
  {
    name: 'admin.users.edit',
    meta: {parent: 'admin.users.index'},
    component: UserEdit
  },
  {
    name: 'admin.groups.index',
    meta: {parent: 'admin.dashboard'},
    component: Groups
  },
  {
    name: 'admin.groups.add',
    meta: {parent: 'admin.groups.index'},
    component: GroupAdd
  },
  {
    name: 'admin.groups.edit',
    meta: {parent: 'admin.groups.index'},
    component: GroupEdit
  },
  {
    name: 'admin.groups.members',
    meta: {parent: 'admin.groups.index'},
    component: GroupMember
  },
  {
    name: 'admin.blog_posts.index',
    meta: {parent: 'admin.dashboard'},
    component: Blog
  },
  {
    name: 'admin.blog_posts.add',
    meta: {parent: 'admin.blog_posts.index'},
    component: BlogAdd
  },
  {
    name: 'admin.blog_posts.edit',
    meta: {parent: 'admin.blog_posts.index'},
    component: BlogAdd
  },
  {
    name: 'admin.blog_posts.category',
    meta: {parent: 'admin.blog_posts.index'},
    component: BlogCategory
  },
  {
    name: 'admin.media.edit',
    meta: {parent: 'admin.dashboard'},
    component: MediaEdit
  },
  {
    path: '',
    component: Settings,
    meta: {parent: 'admin.dashboard'},
    children: [
      {
        name: 'admin.settings',
        redirect: {name: 'admin.settings.profile'}
      },
      {
        name: 'admin.settings.general',
        component: SettingsGeneral
      },
      {
        name: 'admin.settings.profile',
        component: SettingsProfile
      },
      {
        name: 'admin.settings.password',
        component: SettingsPassword
      }
    ]
  },
  {
    name: 'admin.login',
    component: Login
  },
  {
    path: '*',
    component: require('~/pages/errors/404.vue')
  }
]

let locale = store.getters['lang/locale']
// let isDefaultLocale = (
//   locale === store.getters['lang/fallback']
// )
let prefix = ''
// if (!isDefaultLocale) {
//   prefix += '/' + locale
// }
routes = translateRoute(routes, locale, prefix)

function translateRoute (routes, locale, prefix) {
  for (var i in routes) {
    if (routesI18n[locale].hasOwnProperty(routes[i].name)) {
      routes[i].path = prefix + '/' + routesI18n[locale][routes[i].name]
    }
    if (routes[i].hasOwnProperty('children')) {
      routes[i].children = translateRoute(routes[i].children, locale, prefix)
    }
  }

  return routes
}
export default routes
