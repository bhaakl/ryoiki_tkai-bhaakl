# NextGN

## Overview

This admin dashboard and user-facing SPA is built with Vue.js, employing both the Options API and Composition API for a flexible development experience. It integrates Inertia.js to seamlessly connect Laravel server-side controllers with Vue components—handling routing, shared props, and page visits without a separate API. State is centralized in Vuex, enabling predictable, namespaced modules for transactions, products, tasks, and user sessions.

## Features

- **Dynamic Admin Panels**: Role-based modules (`CashbackTransactions`, `OwnerAccount`, `Rewards`, `Tasks`, `System`) with CRUD operations, data tables, and export dialogs.
- **Componentized Layouts**: Shared containers (`TheContainer`, `TheHeader`, `TheSidebar`) and atomic UI components (`Checkbox`, `Pagination`, `Slider`, `DropdownMenu`, `BaseModal`).
- **State Management**: Vuex for global store with strict mode, persisted plugins, and modular structure.
- **Routing & Navigation**: Inertia.js for server-driven navigation with client-side transitions; leverages Option API and Composition API to define page components and lifecycle hooks.
- **Server Integration**: Inertia adapters handle form submissions, validation errors, and flash messages via `useForm`, reducing boilerplate.
- **Responsive Design**: Tailwind CSS mobile-first utility classes and custom breakpoints.
- **Modals & Interactions**: Task dialogs, export modals, and form overlays with `<Transition>` effects.
- **Charts & Analytics**: Bar, pie, and line charts via Chart.js wrapped in Vue components.

## Developer Experience

As a Vue.js developer, I have:

- **Component Authoring**: Created 100+ Single-File Components (`.vue`) using Options API for clear separation of concerns (data, methods, computed) and Composition API (`<script setup>`) for reusable logic and type safety.
- **Vuex Expertise**: Structured namespaced stores for auth, transactions, products, tasks; integrated plugins for persistence and adhered to strict mode for mutation enforcement.
- **Inertia.js Mastery**: Configured layouts, managed shared props (auth, flashes), and leveraged `useForm` for declarative form state and server-side validation.
- **Routing Strategy**: Defined page components using Option API and Composition API hooks; Inertia-driven navigation eliminating manual API calls.
- **Performance Tuning**:
  - Adopted route-level code-splitting and lazy component loading to reduce initial bundle size by ~30%.
- **Atomic Design**: Built a design system with Tailwind CSS utility classes and modular Vue components for buttons, inputs, modals, and tables.
- **Testing & CI**:
  - CI pipeline in GitHub Actions handling lint (ESLint, Prettier), tests, and build.
- **Developer Tools**: Integrated Vue Devtools for real-time debugging and performance profiling.