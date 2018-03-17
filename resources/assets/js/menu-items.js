module.exports = [
  {
    type: 'item',
    isHeader: true,
    name: 'MAIN NAVIGATION'
  },
  {
    type: 'tree',
    icon: 'tachometer-alt',
    name: 'Dashboard',
    router: {
      name: 'admin.dashboard'
    }
  },
  {
    type: 'tree',
    icon: 'user',
    name: 'Users',
    items: [
      {
        type: 'item',
        icon: 'star',
        name: 'List',
        router: {
          name: 'admin.users.index'
        }
      },
      {
        type: 'tree',
        icon: 'star',
        name: 'Add'
      }
    ]
  },
  {
    type: 'tree',
    icon: 'object-group',
    name: 'Groups',
    items: [
      {
        type: 'item',
        icon: '',
        name: 'List',
        router: {
          name: 'admin.groups.index'
        }
      },
      {
        type: 'item',
        icon: '',
        name: 'Add',
        router: {
          name: 'admin.groups.add'
        }
      }
    ]
  }
]
