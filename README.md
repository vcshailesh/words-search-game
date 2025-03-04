# Word Search Game

A web-based word search puzzle game built with Laravel, Livewire, and Tailwind CSS.

## Features

- Simple registration with name and email
- Interactive word search grid
- Real-time word finding and validation
- Timer tracking
- Progress saving
- Responsive design for desktop and mobile
- Dark mode support

## Prerequisites

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js & NPM

## Installation

1. Clone the repository

bash
git clone git@github.com:vcshailesh/words-search-game.git
cd words-search-game

2. Install PHP dependencies
```bash
composer install
```

3. Install and compile frontend assets
```bash
npm install
npm run dev
```

4. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=word_search
DB_USERNAME=your_username
DB_PASSWORD=your_password

SESSION_DRIVER=database
```

6. Run migrations and seeders
```bash
php artisan migrate:fresh --seed
```

## Game Structure

- `app/Livewire/Game/WordSearch.php` - Main game component
- `app/Services/WordSearchService.php` - Grid generation logic
- `database/seeders/WordListSeeder.php` - Word lists for the game
- `resources/views/livewire/game/word-search.blade.php` - Game interface

## Database Tables

- `users` - Player information
- `word_lists` - Collections of words by difficulty
- `words` - Individual words for the game
- `games` - Game progress and statistics
- `sessions` - User session management

## Usage

1. Register with your name and email
2. Start playing immediately
3. Find words by clicking and dragging
4. Words can be placed:
   - Horizontally
   - Vertically
   - Diagonally

## Development

To start the development server:

```bash
php artisan serve
```

For hot-reloading of assets:
```bash
npm run dev
```

## Testing

Run the test suite:
```bash
php artisan test
```

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License.