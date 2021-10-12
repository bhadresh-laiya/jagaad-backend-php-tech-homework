# Jagaad Backend PHP tech homework
Backend PHP tech homework - Jagaad | Developed by **[Bhadresh Laiya](mailto:blaiya18@gmail.com)**

## Deploy the app

I have created this application using [`Laravel v5.7`](https://laravel.com/docs/5.7)

1. Clone the repository: `https://github.com/bhadresh-laiya/jagaad-backend-php-tech-homework.git`
2. Install composer dependencies using: `composer install`
3. Create or rename the .env file: `cp .env.example .env` or use existing one which are with/in repository.
4. Create the data base: `php artisan db:create`
5. Run the migrations: `php artisan migrate`
6. Seed the database: `php artisan db:seed`
7. Set up or copy paste below two lines into `.env` file if not exists or configure.

```
OWN_BASE_URI=http://api.weatherapi.com/v1
OWM_API_KEY=99ca6caa3f784572a6a112303211110
``` 

Run the application using `php artisan serve` at `http://localhost:8000` or with your development environment wise.

## Step-1 | Development

### Get two days of forecast
```
GET http://localhost:8000/cities-weather
```
With this endpoint you can get two days of forecast for the cities found on the [musement](https://www.musement.com/) website using [`forecast.json`](http://api.weatherapi.com/v1/forecast.json?key=[your-key]&q=[lat],[long]&days=2) as per your suggestion.

Result / print to STDOUT will be:
```
Processed city Amsterdam - Netherlands | Moderate rain - Patchy rain possible
Processed city Paris - France | Partly cloudy - Sunny
Processed city Milan - Italy | Sunny - Sunny
Processed city Barcelona - Spain | Sunny - Sunny
...
...
...
```

If you need any help/support while deploying application, then let me know I will help you with installation. 

## Step 2 | API design (no code required)

Now that we have a service that reads the forecast for a city, we want to save this info in the Musement's API. The endpoint that receives information about weather for a city **does not exist**, we only have these 2 endpoints for cities

```
GET /api/v3/cities
GET /api/v3/cities/{id}
```

Please provide the design for:
- endpoint/s to set the forecast for a specific city
- endpoint/s to read the forecast for a specific city

Please consider that we need to answer questions like : 

 - What's the weather in [city] for today ?
 - What's the weather in [city] for tomorrow ? 
 - What's the weather in [city] for [day] ?

For each endpoint provide all required information: endpoint, payload, possible responses etc and description about the behavior.

### API design/solution

```json
"/cities/{cityId}/forecasts": {
    "post": {
        "description": "Save the forecasts for the specified city",
        "parameters": [
            {
                "name": "cityId", 
                "description": "ID of the city"
            }
        ],
        "requestBody": {
            "description": "Forecast post request",
            "content": {
                "application/json": {
                    "schema": {
                        "forecasts": [
                            {
                                "date": "2021-10-12",
                                "condition": "Overcast"
                            },
                            {
                                "date": "2021-10-13",
                                "condition": "Moderate snow"
                            }
                        ]
                    }
                }
            }
        },
        "responses": {
            "201": {
                "description": "Returned when successful",
                "content": {
                    "application/json": {
                        "schema": {
                            "description": "Resources created"
                        }
                    }
                }
            },
            "404": {
                "description": "Returned when resource is not found"
            },
            "503": {
                "description": "Returned when the service is unavailable"
            }
        }
    },
    "get": {
        "description": "Get the forecasts for a city in the period specified between from_date and to_date. If from_date and to_date are not setted then are returned all the forecasts from the date of the request'",
        "parameters": [
            {
                "name": "cityId",
                "description": "ID of the city"
            },
            {
                "name": "from_date",
                "description": "The date from which the forecasts should be retrieved"
            },
            {
                "name": "to_date",
                "description": "The date to which the forecasts should be retrieved"
            },
        ],
        "responses": {
            "200": {
                "description": "Returned when successful",
                "content": {
                    "application/json": {
                        "schema": {
                            "forecasts": [
                                {
                                    "date": "2021-10-12",
                                    "condition": "Overcast"
                                },
                                {
                                    "date": "2021-10-13",
                                    "condition": "Light snow"
                                },
                                {
                                    "date": "2021-10-14",
                                    "condition": "Moderate snow"
                                }
                            ]
                        }
                    }
                }
            },
            "404": {
                "description": "Returned when resource is not found"
            },
            "503": {
                "description": "Returned when the service is unavailable"
            }
        }
    }
}
"/cities/{cityId}/forecasts/{currently}": {
    "get": {
        "description": "Get the forecast from a given city of today or tomorrow",
        "parameters": [
            {
                "name": "cityId",
                "description": "ID of the city"
            },
            {
                "name": "currently",
                "description": "Forecasts for today or tomorrow?",
                "enum": [
                    "today",
                    "tomorrow"
                ]
            }
        ],
        "responses": {
            "200": {
                "description": "Returned when successful",
                "content": {
                    "application/json": {
                        "schema": {
                            "forecasts": [
                                {
                                    "date": "2021-10-12",
                                    "condition": "Overcast"
                                }
                            ]
                        }
                    }
                }
            },
            "404": {
                "description": "Returned when resource is not found"
            },
            "503": {
                "description": "Returned when the service is unavailable"
            }
        }
    }
}
```