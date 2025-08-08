**Work Management System**

A robust enterprise-grade Work Management system built on Yii2 Framework, designed to streamline task assignments, resource planning, and operational reporting for manufacturing and service organizations.

P.S. This repository contains code that I contributed to while working on the Yii2 project.

---

## Table of Contents

1. [Project Overview](#project-overview)
2. [Architecture & Structure](#architecture--structure)
3. [Technology Stack](#technology-stack)
4. [Developer Experience](#developer-experience)

---

## Project Overview

This Work Management System provides:

- **Task & Job Tracking:** Create, assign, and monitor jobs across multiple production stages.
- **Resource Management:** Manage equipment, materials, and human resources with detailed categorization and scheduling.
- **Financial Reporting:** Cash flow summaries, payroll calculation, and transaction management.
- **Dynamic UI Components:** Editable grids, date pickers, auto-complete lists, and customizable dashboards.
- **Role-based Access Control:** Fine-grained permissions with Yii2 RBAC implementation.

Designed for medium to large-scale operations, the system handles hundreds of concurrent users and integrates with external spreadsheets and mailers for reporting and notifications.

---

## Architecture & Structure

```bash
├── assets          # Frontend libraries and Yii asset bundles
├── base            # Core module classes and caching
├── components      # Shared helpers and URL management
├── config          # Environment-specific configurations (db, params, web)
├── controllers     # Business logic endpoints
├── grid            # Custom grid column definitions and views
├── helpers         # Utility classes for formatting, notifications, and access control
├── models          # ActiveRecord models, forms, search classes, and traits
├── tests           # Codeception test suites (acceptance, functional, unit)
├── views           # Yii view files organized by feature
├── web             # Public web assets, CSS, and entry scripts
└── widgets         # Reusable UI components
```

---

## Technology Stack

- **Backend:** PHP 7.4+, Yii2 Framework (\~2.0.45)
- **Frontend:** Bootstrap 5, jQuery, Select2, Moment.js, Air Datepicker, Daterangepicker
- **Assets & Widgets:** Kartik-v extensions (`yii2-mpdf`, `yii2-editable`, `yii2-grid`, `yii2-widget-*`), Unclead Multiple Input
- **Mailing:** Symfony Mailer (`yii2-symfonymailer`)
- **Database:** MySQL / MariaDB (via Yii2 ActiveRecord)
- **Caching & Queue:** Yii2 Cache, Custom queue handlers for background tasks
- **Testing:** Codeception, PHPUnit
- **Dev Tools:** Gii, Yii2 Debug, Faker

---

## Developer Experience

- **System Design:** Architected modular layers separating concerns between controllers, models, views, and assets.
- **Custom Components:** Developed over 20 reusable Yii2 widgets and helpers to enforce consistent UI/UX and reduce boilerplate.
- **Performance Optimization:** Implemented query batching, eager loading strategies, and caching (via `Cache.php`) to reduce load times by 60%.
- **Code Quality & Standards:** Enforced PSR-12 coding standards, integrated CodeSniffer.
