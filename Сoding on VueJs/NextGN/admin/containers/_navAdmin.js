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
        name: 'Магазин',
        url: '/admin/goods',
        icon: 'cil-library'
      },
      {
        _name: 'SidebarNavItem',
        name: 'Пользователи',
        url: '/admin/users',
        icon: 'cil-user'
      },
      {
        _name: 'SidebarNavItem',
        name: 'Тестировщики',
        url: '/admin/all-testers',
        icon: 'cil-people'
      },
      {
        _name: 'SidebarNavItem',
        name: 'Top Live-таблица',
        url: '/admin/top-testers',
        icon: 'cil-people'
      },
      // {
      //    _name: 'SidebarNavItem',
      //    name: 'Роли',
      //    url: '/admin/roles',
      //    icon: 'cil-task',
      // },
      // {
      //    _name: 'CSidebarNavTitle',
      //    _children: ['Данные'],
      // },
      {
        _name: 'SidebarNavItem',
        name: 'Все продукты',
        url: '/admin/all-products',
        icon: 'cil-library'
      },
      {
        _name: 'SidebarNavItem',
        name: 'Видеоинструкции',
        url: '/admin/video',
        icon: 'cil-library'
      },
      {
        _name: 'SidebarNavItem',
        name: 'Типы занятости',
        url: '/admin/occupations',
        icon: 'cil-av-timer'
      },
      {
        _name: 'SidebarNavItem',
        name: 'Интересы',
        url: '/admin/person-intrest'
      },
      {
        _name: 'SidebarNavItem',
        name: 'Вопросы',
        url: '/admin/questions',
        icon: 'cil-comment-bubble'
      },
      {
        _name: 'SidebarNavItem',
        name: 'Текстовые страницы',
        url: '/admin/text-pages',
        icon: 'cil-text'
      },
      {
        _name: 'SidebarNavItem',
        name: 'CMS',
        url: '/admin/cms-pages',
        icon: 'cil-text'
      },
      {
        _name: 'SidebarNavItem',
        name: 'Настройки',
        url: '/admin/settings',
        icon: 'cil-cog'
      },
      {
        _name: 'SidebarNavItem',
        name: 'Шаблоны для SMS',
        url: '/admin/mailing/sms-templates',
        icon: 'cil-short-text'
      }
    ]
  }
]
