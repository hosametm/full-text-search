# Laravel Full-Text Search Project

A Laravel application demonstrating MySQL full-text search capabilities with weighted relevance scoring and highlighting.

## Features

-   Full-text search across posts (title and body)
-   Weighted relevance scoring (title matches weighted higher)
-   Result highlighting
-   Pagination of search results
-   Database seeding with 100,000 sample posts

## Installation

1. Clone the repository
2. Install dependencies:

```bash
composer install
npm install
```

3. Create and configure your `.env` file:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in `.env`. The project uses SQLite by default:

```
DB_CONNECTION=sqlite
```

5. Run migrations and seed the database:

```bash
php artisan migrate
php artisan db:seed --class=PostSeeder
```

## Project Structure

### Key Files

-   `app/Models/Post.php` - Post model
-   `app/Http/Controllers/PostController.php` - Search functionality
-   `database/migrations/2025_07_31_131805_create_posts_table.php` - Posts table with full-text indexes
-   `database/factories/PostFactory.php` - Factory for generating sample posts
-   `database/seeders/PostSeeder.php` - Seeder to populate database

### API Endpoints

#### Search Posts

```
GET /search?q={search_term}
```

Parameters:

-   `q` - Search term to query posts

Response: JSON with paginated results including:

-   Matched posts with highlighted search terms
-   Relevance score
-   Pagination metadata

## Development

Run the development server:

```bash
php artisan serve
```

For frontend assets:

```bash
npm run dev
```

## Testing

Run tests with:

```bash
php artisan test
```

## Features Details

### Full-Text Search Implementation

The search functionality uses MySQL's full-text search capabilities with:

1. Weighted scoring:

    - Title matches are weighted 2x more than body matches
    - Uses MySQL's BOOLEAN MODE for partial word matching

2. Result highlighting:

    - Matched terms are wrapped in `<b>` tags
    - Case-insensitive highlighting

3. Security:
    - Input sanitization for search terms
    - Protection against SQL injection

### Database Schema

The posts table includes full-text indexes on:

-   title
-   body
-   Combined title + body

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('body');
    $table->timestamps();
    $table->fullText(['title']);
    $table->fullText(['body']);
    $table->fullText(['title', 'body']);
});
```
