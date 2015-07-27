Antragsgrün v3
==============

Antragsgrün 3 is the third generation of the Motion-CMS built for the german green party.
It's a complete rewrite of the second generation and has some major advantages:
* It's much more flexible on the structure of motions and wording. Motions event support image uploading now.
* If follows test-driven design, using both unit and acceptance tests
* It's based completely on HTML and gets rid of the obsolete BBCode
* It's based on the more Yii2-framework
* The design of the motion supporter table is much cleaner and does not depend on the user database anymore
* Many more small improvements


***Current Build-Status***

[![Build Status](http://phpci.hoessl.eu/build-status/image/1?branch=v3)](http://phpci.hoessl.eu/build-status/view/1?branch=v3)

Required Software (Debian Linux)
--------------------------------
```bash
apt-get install php5-cli php5-fpm php5-mysqlnd php5-mcrypt php5-intl php5-curl
```

Optional, for LaTeX/XeTeX-based PDFs:
```bash
apt-get install texlive-lang-german texlive-latex-base texlive-latex-recommended texlive-latex-extra texlive-humanities texlive-fonts-recommended texlive-xetex
```

Command Line Commands
---------------------

Force a new password for an user:
```bash
./yii admin/set-user-password user@example.org mynewpassword
```

Flush all caches of a specific consultation:
```bash
./yii admin/flush-consultation-caches subdomain consultationPath
```

Flush all caches of the whole system:
```bash
./yii admin/flush-all-consultation-caches
```


Development Setup
-----------------

```bash
curl -sS https://getcomposer.org/installer | php
./composer.phar global require "fxp/composer-asset-plugin:1.0.0"
./composer.phar install
```


```bash
scss --precision 9
```


Running using docker
--------------------

```bash
cd docker-vagrant/
docker build -t antragsgruen1 -f Dockerfile .
docker run -p 80:80 --name antragsgruen1 -d antragsgruen1
```


Testing
-------

* Install [PhantomJS](http://phantomjs.org/download.html)
* For the automatical HTML validation, Java needs to be installed and the vnu.jar file from the [Nu Html Checker](https://validator.github.io/validator/) located at /usr/local/bin/vnu.jar.
* For the automatical accessibility validation, [Pa11y](http://pa11y.org/) needs to be installed.
* Start PhantomJS: phantomjs --webdriver=4444
* Start debug server: ```bash
tests/start_debug_server.sh```
* Run all tests: ```bash
vendor/bin/codecept run```
* Run a single acceptence-test: ```bash
vendor/bin/codecept run acceptance MotionCreateCept```

