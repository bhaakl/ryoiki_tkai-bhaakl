# E-commerce microservices

## Overview

This e-commerce microservices platform provides a scalable, high-performance backend for managing products, catalogs, and business logic. Built with Laravel and an event-driven architecture, it handles complex workflows such as payments, subscriptions, and inter-service communication with RabbitMQ and Redis.

## Features

- **Multi-Module Structure**: Organized into distinct services (`inflow_business_back_main`, `inflow_catalog`, etc.) for clear separation of concerns.
- **Event-Driven Architecture**: Uses RabbitMQ with ACK/NACK, Dead Letter Exchanges, and Event Sourcing to ensure reliable message delivery.
- **Media & Assets**: Centralized handling of images and files with Spatie Media Library.
- **Containerization**: Dockerfiles for PHP and Composer, along with a `docker-compose.yml` for local development.
- **Configuration Management**: Extensive config files covering cache, queue, rabbitmq, multitenancy, and third-party services.
- **Database Schema & Migrations**: Doctrine DBAL–backed migrations, tenant-specific seeders, and parallel migration support.
- **Testing & Quality**: PHPUnit tests, Faker factories, and PHPStan/PHPCodeSniffer ruleset for static analysis.

## Developer Experience

Participated in the creation of an e-commerce platform. I worked on key system components, from architectural design to the implementation of business logic and infrastructure optimization.

**Backend** (Laravel, Event-Driven Architecture)
- **Database Optimization:**
  - Implemented indexes (composite, covering), analyzed performance using **EXPLAIN** and **Slow Query Log**.
  - Used transactions with `REPEATABLE READ` isolation level for critical operations (processing payments, updating subscriptions).
  - Parallelized migrations (`tenants:migrate`) and implemented tenant-specific seeders, which accelerated updates by 30%.
- **Microservice Ecosystem Support:**
  - Organized inter-service communication using **Event-Driven Architecture (RabbitMQ)**:
    - Improved message delivery by 40% using **ACK/NACK**, **Dead Letter Exchanges**, and **Event Sourcing**.
    - Reduced latency by 25% through **Topic Exchanges** and connection caching in **Redis**.
- **Design Patterns and Principles:**
  - Applied **SOLID/DRY/KISS** principles with design patterns such as **Observer** (for notifications), **Command** (for subscription processing), and **State** (for status management).

## Technology Stack

- **Core Framework:** Laravel 11 (PHP 8.3+)
- **Authentication & Authorization:** Laravel Sanctum (token-based), Tymon JWT Auth, Spatie Laravel Permission
- **Data & API:** Spatie Laravel Data for DTOs, API resources
- **Image Handling:** Intervention Image for on-the-fly manipulation
- **Media Management:** Spatie Laravel Medialibrary
- **Multitenancy:** Spatie Laravel Multitenancy
- **State Management:** Spatie Laravel Model States
- **Message Queuing:** RabbitMQ via php-amqplib and laravel-queue-rabbitmq
- **Utility & Integrations:**
  - DaData integration via hflabs/dadata
  - Spatie Laravel Backup & Telescope for monitoring & backups
- **Development Tools:** Laravel Tinker, Laravel Pint (code style), FakerPHP, PHPUnit
- **Queue & Caching:** Redis (default cache), RabbitMQ (job queue)