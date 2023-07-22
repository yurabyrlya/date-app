To run the application locally, follow these steps:

1. Use `docker-compose` to run the app with `docker`. Execute the following command:
```bash
docker-compose up -d
```

2. Once the application is up and running, open `http://localhost:8080` in your web browser.

3. To install the necessary dependencies, run the following commands inside the `date-app` container:

```bash
composer install
npm install
npm run dev
```

By following these instructions, you'll have the application running locally with all the required dependencies installed.
