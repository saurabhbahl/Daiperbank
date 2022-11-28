# Healthy Steps - Diaper Bank Management Application

### Todo List

[https://trello.com/b/qvk8Vmsf/healthy-steps](https://trello.com/b/qvk8Vmsf/healthy-steps)

### Application Requirements

- MariaDB/MySQL
- PHP 7.1
- Composer
- Node.js
- npm (comes with node)
- [PDFTK](https://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/)
    + `apt-get install pdftk`
- [WkHtmlToPDF v0.12.3](https://wkhtmltopdf.org/) -- there's a bug in 0.12.4 with font sizes on OSX...might not be a problem on Ubuntu, but worth noting here.
    + `apt-get install wkhtmltopdf`
    + make sure you update your `.env` with the path to the wkhtmltopdf bin, or configure it in the `config/hsdb.php` configuration file.

## WkHTMLtoPDF Info

Apparently, WkHTMLtoPDF has issues with Ubuntu 16.04 WRT OpenSSL. This is an issue because we render a lot of PDFs by making web requests to
pages + downloading web hosted assets (images, css files, etc).

This is a pretty hot button topic on [wkhtmltopdf/wkhtmltopdf issue #3001](https://github.com/wkhtmltopdf/wkhtmltopdf/issues/3001) starting
all the way back in January 2016.

Skimming, I found a fix that seems to have worked for me, here: https://github.com/wkhtmltopdf/wkhtmltopdf/issues/3001#issuecomment-360177463

I'm not really sure what other ramifications that had, but it fixed the OpenSSL issue in this case (and didn't cause anything else to wet the bed
either, so I'll call it a win).

## Launching Locally

### Additional Requirements

- Valet

**Install Homebrew**

Go to [https://brew.sh](https://brew.sh/) and follow the installation instructions

**Install MariaDB**

1. Open a terminal
2. type `brew install mariadb`, press enter
3. follow the on-screen instructions

**Install PHP 7.1**

1. Open a terminal
2. type `brew homebrew/php/php71`, press enter
3. follow the on-screen instructions

**Install Composer**

Visit [https://getcomposer.org/download](https://getcomposer.org/download/) and follow the Download instructions.

**Install Node.js**

1. Open a terminal
2. type `brew install node`, press enter
3. follow the on-screen instructions

**Install Valet**

1. Open a terminal
2. Type `composer global require laravel/valet`, press enter
3. Type `valet install`, press enter

### Install & Configure Application

1. Clone the application from github to your local machine (usually something like: `cd ~/Sites && git clone git@github.com:jimrubenstein/hs-diaper-bank.git healthy-steps`)
2. `cd healthy-steps && git checkout dev`
3. `cp .env.example .env`
4. Edit the `.env` file with the `DB_USERNAME` and `DB_PASSWORD` for your MariaDB/MySQL user
5. `composer install`
6. `valet link healthy-steps`
7. `php artisan migrate --seed`
8. `npm run dev`

Now, open your browser and try to visit [healthy-steps.dev/login](http://healthy-steps.dev/login). With a small bit of luck, if everything is installed correctly, you'll see the login prompt.

### Login Credentials

*username*: jrubenstein

*password*: hs1234
