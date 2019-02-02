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
        icon: 'star',
        name: 'sidebar.list',
        router: {
          name: 'admin.users.index'
        }
      }
    ]
  },
  {
    type: 'tree',
    icon: 'object-group',
    name: 'sidebar.groups',
    items: [
      {
        type: 'item',
        icon: '',
        name: 'sidebar.list',
        router: {
          name: 'admin.groups.index'
        }
      },
      {
        type: 'item',
        icon: '',
        name: 'sidebar.add',
        router: {
          name: 'admin.groups.add'
        }
      }
    ]
  },
  {
    type: 'tree',
    icon: 'user',
    name: 'sidebar.blog',
    items: [
      {
        type: 'item',
        icon: 'star',
        name: 'sidebar.list',
        router: {
          name: 'admin.blog_posts.index'
        }
      },
      {
        type: 'item',
        icon: 'star',
        name: 'sidebar.add',
        router: {
          name: 'admin.blog_posts.add'
        }
      },
      {
        type: 'item',
        icon: 'star',
        name: 'sidebar.category',
        router: {
          name: 'admin.blog_posts.category'
        }
      }
    ]
  }
]
