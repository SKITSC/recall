![Recall](https://raw.githubusercontent.com/SKITSC/recall/main/img/recall_logo.svg)
<img alt="GitHub" src="https://img.shields.io/badge/LICENSE-GPLv3.0-green">
<img alt="GitHub" src="https://img.shields.io/badge/PHP-%3E%3D5.5-green"><br>

Create backup for all your recorded calls, locally on your server.
## Features

* Self-Hosted, dockerized application
* Simple and responsive web interface
* 100% customizable (check out /public/scss/themes.scss)
* Automatically downloads and store .mp3 files directly on your system
* Can search by number and listen to calls directly on the web interface
* Screenshots at /img

## Browser support

![Chrome](https://raw.githubusercontent.com/alrra/browser-logos/master/src/chrome/chrome_48x48.png) | ![Firefox](https://raw.githubusercontent.com/alrra/browser-logos/master/src/firefox/firefox_48x48.png) | ![IE](https://raw.githubusercontent.com/alrra/browser-logos/master/src/edge/edge_48x48.png) | ![Opera](https://raw.githubusercontent.com/alrra/browser-logos/master/src/opera/opera_48x48.png) | ![Safari](https://raw.githubusercontent.com/alrra/browser-logos/master/src/safari/safari_48x48.png)
--- | --- | --- | --- | --- |
Latest ✔ | Latest ✔ | 10+ ✔ | Latest ✔ | 6.1+ ✔ |

## Install

Start by configuring the .env file correctly, copy the template in .env.example, it will be used in both the docker and not docker environment:

```bash
ENV=PRODUCTION
DB_HOST=plivo_db
DB_PORT=3306
DB_NAME=plivo_backer
DB_USERNAME=root
DB_PASSWORD=password
PLIVO_AUTH_ID=YOUR_AUTH_ID
PLIVO_AUTH_TOKEN=YOUR_AUTH_TOKEN
```

Without Docker:

Import the database, scripts are already in /database.

```bash
git clone https://github.com/SKITSC/plivo_backup.git
cd ./plivo_backup

composer install

cp .env.example .env
sed -i -e 's/DEV/PRODUCTION/g' .env
```

If you are running without docker, be sure to run the scripts in /utils periodically, either in a Windows task Scheduler or a cron job. You could run the fetch_recordings.php every minute and the download_recordings.php every 2 hours or so, depending on your needs.

With Docker:
```bash
git clone https://github.com/SKITSC/plivo_backup.git
cd ./plivo_backup

cp .env.example .env
sed -i -e 's/DEV/PRODUCTION/g' .env

docker-compose up --build

docker exec -it plivo_web bash #attach to web container
docker exec -it plivo_db bash #attach to db container
```
Find the app running at http://localhost:80/

Default credentials are `skitsc:skitsc`

Login and start by synchronizing the database, then a cron job will run and keep you updated every REFRESH_TIME defined in the .env file.

Find your recordings in the /recordings folder, in the format:

```
/recordings
|       
|_______year
        |_______month
                |_______day
                        |_______hour-minute-second.mp3
```

## Local development

However you set up your environment, simply change the proxy option in gulp.js, then you can run:
```
npm run watch
```

## Requirements

* node, npm for local development
* Composer ([installation instructions](https://getcomposer.org/doc/00-intro.md))
* Docker ([installation instructions](https://docs.docker.com/install/))
* Docker Compose ([installation instructions](https://docs.docker.com/compose/install/))

## Contributing

* Create an issue or a pull request, or email us at opensource@skitsc.com

## TO DO

* Add a search by date method inside of /recordings.php page

## License

```text
GNU GENERAL PUBLIC LICENSE v3.0
READ FULL LICENSE IN FILE: COPYING

Copyright (c) 2021 SKITSC

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.
```

Disclaimer: _This is not an officially supported SKITSC products._
