# How to start the app

1. Clone .env.example to .env file
2. Add Alpha Vantage API key to env ALPHA_VANTAGE_API_KEY
3. Run docker compose up
4. Open http://localhost:3000 in the browser to check the prices


## About App

This is Laravel based application which gets crypto prices periodicaly from Alpha Vantage API and stores them in mysql database from where its available through UI and REST api.

# Scheduling jobs

Job which fetches crypto prices from Alpha Vantage API is scheduled through Laravel scheduler which is started on when app continer starts.

## REST api

### GET /commodities

Returns all crypto commodities with live prices
Example response
```json
{
    "commodities": [
        {
            "name": "Bitcoin",
            "symbol": "BTC",
            "currentRate": {
                "rate": 1.22,
                "change": 1.67
            }
        },
        {
            "name": "Ethereum",
            "symbol": "ETH",
            "currentRate": {
                "rate": 2.41,
                "change": -0.82
            }
        }
    ]
}
```

## UI

UI for viewing live crypto prices is available on the homepage 
### GET /


# Testing

Test are created in phpunit. To run the tests please run:

```bash
./vendor/bin/phpunit
```

# Notes
Laravel `Cache` is used to reduce stress on database. Integer id is used as foreign key in rates table instead of commodity symbol for faster indexing.