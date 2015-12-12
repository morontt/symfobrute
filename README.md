# SymfoBrute

Переборщик паролей в симфони-проектах на дефолтных настройках *MessageDigestPasswordEncoder*.
Баловства ради, а не со злым умыслом.

Пароли взяты [отсюда](https://stricture-group.com/files/adobe-top100.txt).

## Использование

Добавляем поля *checked* и *plain_password* в таблицу пользователей. Или создаём новую таблицу по
имеющейся [схеме](./schema.sql) и импортируем в неё данные.

```sql
-- Если нужно добавить поля

ALTER TABLE `user` ADD `checked` TINYINT NOT NULL DEFAULT 0,
    ADD `plain_password` VARCHAR(255) NULL DEFAULT NULL;
```

Далее копируем и редактируем файл настроек (подключение к БД и настройки хеширования):

```sh
cp config.sample.php config.php
```

И запускаем скрипт:

```sh
php force.php
```
