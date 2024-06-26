# Демонстрационный проект

Disclaimer: В этом проекте хотелось бы продемонстрировать используемые практики не только по отношению к базовым навыкам программирования, но и к организации приложений в целом.

Многие вещи продиктованы этим желанием и, надеюсь, не покажутся избыточными для решения небольшого [задания](resources/test_task_202204011832.md).

### Первичная конфигурация локального Docker

```
cp .env.example .env
cp docker-compose.yml.example docker-compose.yml
```

### Запуск в локальном Docker
```
docker-compose up -d
```
В базовой конфигурации приложение отдается через Nginx, БД хранится отдельно.
Nginx настроен отдавать приложение по адресу https://goulash.local/, БД доступна извне по localhost:13306, доступ по credentials из .env (явки и пароли для простоты везде указаны как "app")

### Консоль PHP контейнера
```
docker-compose run php bash
```

### Инициализация приложения в консоли PHP
```
composer install
php artisan key:generate
```

### Загрузка дампа в БД:
```
chmod +x ./database/init/load_dump.sh && ./database/init/load_dump.sh
```

### Метод API для формирования меню пиццы по заданному шаблону рецепта 

https://goulash.local:1443/api/menu/pizza?recipe=DCII

#### Альтернативно, то же самое меню можно вывести через консольную команду:

```
php artisan app:menu pizza dcii
```
