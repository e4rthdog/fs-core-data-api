# FlightSim Core Data API

A simple API for retrieving airport runway data from a SQLite database.

## Overview

This API provides access to airport runway data, including runway identifiers and headings. It requires an API key for authentication and returns data in JSON format.

## Setup Instructions

1. Clone the repository
2. Create a configuration file:
   ```
   cp config.php.example config.php
   ```
3. Edit `config.php` and set a secure API key
4. Ensure the web server points to the `public` directory as the document root
5. Make sure PHP has write permissions to the `data` directory

## API Documentation

### Get Airport Runway Data

```
GET /airports.php?icao=XXXX&key=your_api_key
```

#### Parameters:

- `icao` (required): The ICAO code of the airport (e.g., OOAL)
- `key` (required): Your API key for authentication

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
- 401: Invalid or missing API key
- 404: No data found for the specified ICAO code
- 500: Server error

## Deployment

For production deployment:

1. Only expose the `public` directory to the web server
2. Keep the configuration file and database outside the web root
3. Use a secure, randomly generated API key

### Apache Configuration

```apache
DocumentRoot /path/to/fs-core-data-api/public
<Directory /path/to/fs-core-data-api/public>
    AllowOverride All
    Require all granted
</Directory>
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/fs-core-data-api/public;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
    }
    
    location ~ /\.ht {
        deny all;
    }
}
```

## Security Considerations

- The API is protected with an API key
- Configuration file (`config.php`) should be kept outside the web root
- SQLite database (`data/airports.db`) should not be directly accessible via web
- Only the `public` directory should be exposed to the web server

## Development

This project uses a simple PHP setup with a SQLite database. To add features or modify the API:

1. Make changes to the `airports.php` file or add new endpoints
2. Test thoroughly before deployment
3. Consider adding rate limiting for production use

## License

[Insert your license information here]

## Contact

[Insert your contact information here]
