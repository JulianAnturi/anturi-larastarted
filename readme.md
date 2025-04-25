# 🎞 `Larastarted` by Anturi

A Laravel library designed to speed up your development with automatic generation of models, controllers, migrations, routes, standardized responses, and more. Perfect for RESTful APIs with a clean and professional structure. 🧙‍♂️

---

## Requirements
Install the API base setup with the following command:
```bash
php artisan install:api
```

---

## 🚀 Installation

### Option 1: Use it locally

Clone the repo or add it as a local package, then run:

```bash
composer require anturi/larastarted:dev-main
```

> 🔁 Make sure your branch is `main` or adjust the branch name accordingly.

---

## 🛠️ What does Larastarted do?

With just one command, it automatically generates:

- 🧠 Model (`app/Models`)
- 🎮 Controller (`app/Http/Controllers`)
- 🧱 Migration (`database/migrations`)
- 🚤 API Route (`routes/api.php`)
- 🧹 Base configuration
- 🗑️ Logs with their own table (`logs`)

---

## ✨ Available Commands

### 🧙 `anturi:generate`

```bash
php artisan anturi:generate Post posts
```

This command will generate:

- `Post.php` in `app/Models`
- `PostController.php` in `app/Http/Controllers`
- A migration for `posts`
- A route entry in `routes/api.php`

### 📝 Interactive Questions Example
When running the command, you'll be asked a few questions to customize your resource:

```php
->expectsQuestion('Do you wish to create a migration?', true)
->expectsQuestion('Name of field (leave empty to finish)', 'title')
->expectsQuestion("Select the data type for 'title'", 'string')
->expectsQuestion("Length for 'title'? (leave empty to use default)", '255')
->expectsQuestion("Can the 'title' field be nullable?", false)
->expectsQuestion('Field name (leave empty to finish)', '')
->expectsQuestion('Do you want to add relationships?', false)
->expectsQuestion('Do you want to add a middleware to the route?', false)
```

---

## 🧹 Folder Structure

```
Larastarted/
├── src/
│   ├── Commands/                  → Artisan commands
│   ├── config/                    → Configuration files
│   ├── Controllers/              → Reusable base controllers
│   ├── Generators/               → Generation logic (Model, Controller, etc)
│   ├── Helpers/                  → Reusable services (logs, CRUD, responses)
│   ├── Migrations/               → Internal package migrations
│   ├── Models/                   → Models used by the package
│   ├── Providers/                → Package's Service Provider
│   ├── Publishable/              → Files that can be published to the host app
│   ├── Routes.php                → Auto-injected routes
│   ├── Traits/                   → Useful traits like FieldBuilder
│   └── test/                     → Unit and feature tests
```

---

## 📄 Configuration

The `AnturiServiceProvider` does the magic:

- Registers artisan commands
- Injects routes automatically
- Loads internal migrations
- Publishes configurations

```php
$this->loadMigrationsFrom(__DIR__.'/../Migrations');
$this->loadRoutesFrom(__DIR__.'/../Routes.php');
$this->publishes([...], 'larastarted-config');
```

---

## 📱 Vendor Publishing

It’s highly recommended to publish the vendor files before using:

```bash
php artisan vendor:publish --tag=anturi-larastarted
```

---

## 🧪 Testing

This package includes tests for:

- Generators
- Artisan commands
- Traits

```bash
php artisan test
```

---

## 🧠 Credits

Crafted with 💚 by **Julian (a.k.a. Dimitri Rocket)** 🚀  
Inspired by real-world needs for clean, fast API development.

