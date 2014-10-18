# tinyIt


## Requirements

This application was designed to run on the [apache web server](http://httpd.apache.org/) with [PHP 5 support](http://php.net/) (5.3+ recommended, older versions have not been tested yet).

Other web servers (e.g. nginx) should work as well, but require additional configuration, especially for URL rewriting. You can try to translate the generated htaccess file to a suitable configuration for your web server. Feel free to commit code enhancing compatibility with other server software.

All additional runtime dependencies will be downloaded during the installation. Have a look at the installation instructions for build dependencies.


## Installation

### Step 1: Download

You can get the required files by

 * cloning this repository
 
        git clone https://github.com/tniessen/tinyIt

 * downloading a release or code archive ([current master archive](https://github.com/tniessen/tinyIt/archive/master.zip)) from [GitHub](https://github.com/tniessen/tinyIt). You need to extract the archive before continuing.

### Step 2: Build and prepare

You can build and prepare the application on any system, whether it is the system you are going to run the application on or not. The build process requires [node](http://nodejs.org), [npm](https://www.npmjs.org/), [bower](http://bower.io/) and [grunt](http://gruntjs.com/). These tools are **not required** for running the application.

The following commands will download all dependencies and build the application (within the local directory):

    npm install
    bower install
    grunt

The directory now contains a working setup. Move or copy it to the document directory of your webserver or use it as the document root.

### Step 3: Install
If you open the installation directory in a web browser, you will see the front page of a setup tool. Follow the instructions to complete the installation.

In order to setup the database, the wizard will ask you for database host, credentials and name. Make sure to have an available database ready. As the installation tool currently does not support database migration or reuse of existing tables, there is no guarantee that the installation will succeed if you attempt to reuse tables from a previous installation.

The tool should create all necessary configuration files, including the apache `.htaccess` file. See [Requirements](#Requirements) for information about supported web servers.
