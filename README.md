# FlightSim Core Data API

A simple API for retrieving airport runway data from a SQLite database.

## Overview

This API provides access to airport runway data, including runway identifiers and headings. It returns data in JSON format.

## Project Structure

```
config.php.example         # Sample configuration file
README.md                 # Project documentation
vatinfo-database.sql      # Example SQL for database
/data/
    airports.db           # SQLite database file
/public/
    airports.php          # Main API endpoint for airport runways
    getdata.php           # Additional API/data endpoint
    getevents.php         # Additional API/data endpoint
    getmetar.php          # Additional API/data endpoint
    config.php            # Actual configuration (not in version control)
    composer.json         # Composer dependencies (if any)
    composer.lock         # Composer lock file
    vatinfo-panels.php    # Additional PHP panel endpoint
    vatinfo-panels-config-example.php # Example config for panels
```

## Setup Instructions

1. Clone the repository
2. Create a configuration file:
   ```
   cp config.php.example config.php
   ```
3. Edit `config.php` if needed
4. Ensure the web server points to the `public` directory as the document root
5. Make sure PHP has write permissions to the `data` directory

## API Documentation

### Get Airport Runway Data

```
GET /airports?icao=XXXX
```

Or with the full path on shared hosting:
```
GET /api/fs-core-data-api/airports?icao=XXXX
```

#### Parameters:

- `icao` (required): The ICAO code of the airport (e.g., OOAL)

#### Response Format:

```json
{
  "airport_icao": "OOAL",
  "airport_name": "Antwerp International Airport",
  "runways": [
    {
      "runway": "01",
      "heading_degrees": 10
    },
    {
      "runway": "19",
      "heading_degrees": 190
    }
  ]
}
```

#### Error Responses:

- 400: Missing required parameter
- 404: No data found for the specified ICAO code
- 500: Server error

## Deployment

For production deployment:

1. Only expose the `public` directory to the web server
2. Keep the configuration file and database outside the web root

### Apache Configuration

```apache
DocumentRoot /path/to/fs-core-data-api/public
<Directory /path/to/fs-core-data-api/public>
    AllowOverride All
    Require all granted
</Directory>
```
## Development

This project uses a simple PHP setup with a SQLite database. To add features or modify the API:

1. Make changes to the `airports.php` file or add new endpoints (such as `getdata.php`, `getevents.php`, `getmetar.php`, or `vatinfo-panels.php`)
2. Test thoroughly before deployment
3. Consider adding rate limiting for production use