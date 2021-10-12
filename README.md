# Jagaad Backend PHP tech homework
Backend PHP tech homework - Jagaad | Developed by Bhadresh Laiya

## Deploy the app
1. Clone de repository: `https://github.com/bhadresh-laiya/jagaad-backend-php-tech-homework.git`
2. Install composer dependencies: `composer install`
3. Create or rename the .env file: `cp .env.example .env` or use existing one which are with/in repository.
4. Create the data base: `php artisan db:create`
5. Generate the migrations: `php artisan migrate`
6. Seed the database: `php artisan db:seed`
7. Set up or copy paste below two lines into `.env` file if not exists or configure.

```
OWN_BASE_URI=http://api.weatherapi.com/v1
OWM_API_KEY=99ca6caa3f784572a6a112303211110
``` 

Run the application using `php artisan serve` at `http://localhost:8000` or with your development environment wise.

## Step-1 Development

### Get two days of forecast
```
GET http://localhost:8000/cities-weather
```
With this endpoint you can get two days of forecast for the cities found on the [musement](https://www.musement.com/) website.

Example:
```
Processed city Amsterdam - Netherlands | Moderate rain - Patchy rain possible
Processed city Paris - France | Partly cloudy - Sunny
Processed city Milan - Italy | Sunny - Sunny
Processed city Barcelona - Spain | Sunny - Sunny
...
...
...
```
