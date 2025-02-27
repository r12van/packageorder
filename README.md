# Package Order

## Installation Steps

1. **Update Composer Dependencies**
    ```bash
    composer update
    ```

2. **Copy Environment File**
    ```bash
    cp .env.example .env
    ```

3. **Update Application Key**
    ```bash
    php artisan key:generate
    ```

4. **Run Migrations**
    ```bash
    php artisan migrate
    ```

5. **Seed the Database**
    ```bash
    php artisan db:seed
    ```
