## Instrukcja

Projekt wykorzystuje Dockera, po pobraniu repozytorium należy utworzyć plik `.env` z pliku `.env.example` a następnie zainstalować wymagane zależności poniższą komendą (zgodnie z dokumentacją Laravel Sail)

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

Do uruchamiania migracji, seederów, testów będzie używana komenda `sail`, jest to alias dla `vendor/bin/sail`:

```bash
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
```

Aby uruchomić projekt wykonujemy:

```bash
sail up -d
```

Do wykonania migracji:

```bash
sail artisan migrate:fresh
```

Do seedowania bazy:

```bash
sail artisan db:seed
```

Wykonanie testów:

```bash
sail artisan test --testsuite=Feature --stop-on-failure --env=testing
```
