# ⋅ RECALL
Create backup for all your recorded calls, locally on your server.

Don't ever lose a recorded call!

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

## Features

* Simple and responsive web interface
* 100% customizable (check out /public/scss/themes.scss)
* Downloads and store the .mp3 files directly on your system
* Can search by number and listen to calls directly on the web interface

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