# plivo_backup
Create backup for all your recorded calls, locally on your server.

## Install

Without Docker:
```bash
git clone https://github.com/SKITSC/plivo_backup.git
cd ./plivo_backup
composer install

cp .env.example .env
sed -i -e 's/DEV/PRODUCTION/g' .env
```

With Docker:
```bash
git clone https://github.com/SKITSC/plivo_backup.git
cd ./plivo_backup

cp .env.example .env
sed -i -e 's/DEV/PRODUCTION/g' .env

docker-compose up --build
```

Deploy credentials for the app is `skitsc:skitsc`

Login and synchronize start by synchronizing the database, then a cron job will run and keep you updated every REFRESH_TIME defined in the .env file.

Find your recordings in the /recordings folder, in the format:

/recording
|
|___________year
            |___________month
                        |___________day
                                    |___________hour-minute-second.mp3


## Features

* Feature 1
* Feature 2

## Requirements

* Composer ([installation instructions](https://getcomposer.org/doc/00-intro.md))
* Docker ([installation instructions](https://docs.docker.com/install/))
* Docker Compose ([installation instructions](https://docs.docker.com/compose/install/))

## Contributing

* Create an issue or a pull request, or email us at opensource@skitsc.com

## License

```text
MIT License

Copyright (c) 2021 SKITSC

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

Disclaimer: _This is not an officially supported SKITSC products._