export default [
  {
    _name: 'CSidebarNav',
    _children: [
      {
        _name: 'CSidebarNavTitle',
        _children: ['Данные']
      },
      {
        _name: 'SidebarNavItem',
        name: 'Продукты',
        url: '/admin/moderator/products',
        icon: 'cil-library'
      },
      {
        _name: 'SidebarNavItem',
        name: 'Тестировщики',
        url: '/admin/moderator/testers',
        icon: 'cil-people'
      }
    ]
  }
]
