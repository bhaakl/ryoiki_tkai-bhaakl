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
        name: 'Тестировщики',
        url: '/admin/testers',
        icon: 'cil-people',
        permissions: ['view_testers']
      },
      {
        _name: 'SidebarNavItem',
        name: 'Продукты и отчеты',
        url: '/admin/products',
        icon: 'cil-library',
        permissions: ['view_products']
      },
      {
        _name: 'SidebarNavItem',
        name: 'Задания',
        url: '/admin/tasks',
        icon: 'cil-task',
        permissions: ['view_tasks']
      },
      {
        _name: 'SidebarNavItem',
        name: 'Вознаграждения',
        url: '/admin/rewards',
        icon: 'cil-gift',
        permissions: ['view_rewards']
      },
      {
        _name: 'SidebarNavItem',
        name: 'Начисления Cashback',
        url: '/admin/transactions/cashback',
        icon: 'cil-money',
        permissions: ['view_transactions_cashback']
      }
      // {
      //   _name: 'SidebarNavItem',
      //   name: 'Начисление Coins',
      //   url: '/admin/transactions/coins',
      //   icon: 'cil-money',
      //   permissions: ['view_transactions_coins']
      // }
    ]
  }
]
