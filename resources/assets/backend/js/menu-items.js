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
      },
      {
        type: 'tree',
        icon: 'star',
        name: 'sidebar.add'
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
          name: 'admin.blog.index'
        }
      },
      {
        type: 'tree',
        icon: 'star',
        name: 'sidebar.add'
      }
    ]
  },
]
