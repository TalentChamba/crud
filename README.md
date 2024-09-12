# Blog CMS

This is a Content Management System (CMS) for blog articles built with Laravel. It includes user authentication, roles, permissions, and follows best practices for code structure, security, and performance.

## Features

1. Authentication & User Management
   - User registration and login
   - Role-based access control (Admin and Author roles)

2. CRUD for Blog Posts
   - Create, Read, Update, and Delete blog posts
   - Soft deletes for posts

3. Post Visibility & Scheduling
   - Publish or draft posts
   - Schedule posts for future publication

4. Validation & Security
   - Form validation for blog posts
   - Role-based authorization
   - CSRF protection

5. Tagging System for Blog Posts
   - Add multiple tags to posts
   - Filter posts by tags

6. User Activity Log
   - Track user actions (create, update, delete posts)

7. API for Blog Posts (NOT DONE)
   - Fetch list of blog posts
   - Create a post
   - Delete a post
   - API token authentication

## Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Run `php artisan key:generate`
5. Run `php artisan migrate --seed`
6. Run `npm install && npm run dev`
7. Serve the application with `php artisan serve`

## Usage

1. Register a new user account
2. Log in to the system
3. Create, edit, and manage blog posts
4. Add tags to posts
5. Schedule posts for future publication

## Contributing

Please read the CONTRIBUTING.md file for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the LICENSE.md file for details.
