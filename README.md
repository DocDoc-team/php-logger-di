# Logger from DI

[![Build Status](https://travis-ci.org/DocDoc-team/php-logger-di.svg?branch=main)](https://travis-ci.org/DocDoc-team/php-logger-di)
[![Code Coverage](https://scrutinizer-ci.com/g/DocDoc-team/php-logger-di/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/DocDoc-team/php-logger-di/?branch=main)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/DocDoc-team/php-logger-di/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/DocDoc-team/php-logger-di/?branch=main)


`composer require docdoc/psr-logger`

Предоставлят типовую конфигуацию для PSR-11 логера для Symfony DI компонента.
Логгер пишет логи один раз лог коллектор, который уже пишет в нужные пункты назначения асинхронно, не влияя на время php приложения.


```yaml
parameters:
    app.env: '%env(default:app.env.prod:APP_ENV)%'
    app.env.prod: prod

    env(LOG_SOCKET): "udp://127.0.0.1:12201"

    logger.name: default
    logger.socket:
        address: '%env(LOG_SOCKET)%'
        connectTimeout: 0.5
        setWritingTimeout: 0.5


services:
    _defaults:
        autowire: true
        autoconfigure: false
        public: false

    DocDoc\Logger\Processor\EsIndexProcessor: ~
    DocDoc\Logger\Processor\CleanContextProcessor: ~

    DocDoc\LogTraceProcessor\TraceProcessor: ~
    DocDoc\LogTraceProcessor\TraceFormatter: ~

    DocDoc\Logger\SocketJsonHandler:
        arguments: ["%logger.socket%"]
        calls:
            - pushProcessor: ['@DocDoc\Logger\Processor\EsIndexProcessor']
            - pushProcessor: ['@DocDoc\Logger\Processor\CleanContextProcessor']

    Psr\Log\LoggerInterface:
        class: Monolog\Logger
        public: true
        arguments:
            $name: "@=parameter('app.env') === 'prod' ? parameter('logger.name') : parameter('app.env')~' '~parameter('logger.name')"
            $handlers: ['@DocDoc\Logger\SocketJsonHandler']
            $processors: ['@DocDoc\Logger\Processor\EsIndexProcessor', '@DocDoc\LogTraceProcessor\TraceProcessor']
```

Файл конфигурации подключается лоадером контейнера, либо он может быть подключен из файла конфигурации yml
```yaml
parameters:
    ...
services:
    ...

imports:
    - { resource: ../vendor/docdoc/php-logger-di/src/logger.yml } # путь до папки vendor относительно файла кофнига di
```

Адрес подключения к сокету лог коллектора определяет `ENV` переменной `LOG_SOCKET`

Логгер может работать с `tcp`, `udp`, `unix` сокетом и отправлять туда данные в json формате. В качестве лог коллектора может быть использовать `fluend`, `vector`, либо любой иной лог коллектор с возможнгосью приема логов из сокета.

В имя канала подмешивается `app.env` параметр для всех сред, кроме `prod`, чтобы отделять по каналу логи с разных сред, на случай, если все идет в одно хранилище логов. 

По умолчанию активированы некоторые процессоры как для всего логгера, так и процессоры для обработчика SocketJsonHandler.
