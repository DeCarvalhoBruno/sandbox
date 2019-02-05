export default [
  {
    type: 'item',
    isHeader: true,
    name: 'sidebar.main_nav'
  },
  {
    type: 'tree',
    icon: 'tachometer-alt',
    name: 'sidebar.dashboard',
    router: {
      name: 'admin.dashboard'
    }
  },
  {
    type: 'tree',
    icon: 'user',
    name: 'sidebar.users',
    items: [
      {
        type: 'item',
        icon: 'list-ol',
        name: 'sidebar.list',
        router: {
          name: 'admin.users.index'
        }
      }
    ]
  },
  {
    type: 'tree',
    icon: 'users',
    name: 'sidebar.groups',
    items: [
      {
        type: 'item',
        icon: 'list-ol',
        name: 'sidebar.list',
        router: {
          name: 'admin.groups.index'
        }
      },
      {
        type: 'item',
        icon: 'plus-square',
        name: 'sidebar.add',
        router: {
          name: 'admin.groups.add'
        }
      }
    ]
  },
  {
    type: 'tree',
    icon: 'newspaper',
    name: 'sidebar.blog',
    items: [
      {
        type: 'item',
        icon: 'list-ol',
        name: 'sidebar.list',
        router: {
          name: 'admin.blog_posts.index'
        }
      },
      {
        type: 'item',
        icon: 'plus-square',
        name: 'sidebar.add',
        router: {
          name: 'admin.blog_posts.add'
        }
      },
      {
        type: 'item',
        icon: 'sitemap',
        name: 'sidebar.category',
        router: {
          name: 'admin.blog_posts.category'
        }
      }
    ]
  },
  {
    type: 'tree',
    icon: 'image',
    name: 'sidebar.media',
    router: {
      name: 'admin.medias.index'
    }
  }
]
