# TeradataAsterWebsite
Simple PHP web application that uses the Teradata Aster Cluster via an ODBC connector.

## Requirements
* Apache2 (don't know if it works with ngnix)
* PHP 5.6

## Setup of the project on Linux
1. Download and install [unixODBC](http://www.unixodbc.org/):

        sudo apt-get install unixodbc
        sudo apt-get install php5-odbc

2. Download and extract the **Aster Client Tools (x32 or x64) for Linux** from here:

        http://downloads.teradata.com/download/aster/aster-client-tools-for-linux

    Inside the extracted directory navigate at this directory:

        ./stage/home/beehive/clients-linux64/

    here you should find this file

        clients-odbc-linux64.tar.gz

    extract it and put its content into the **/usr/local/lib** directory of your system, so it should be like this:

        /usr/local/lib/stage/clients-odbc-linux64/..

3. Edit the **/etc/odbc.ini** and put the following content in it, modify it to meet your parameters:

        [ODBC Data Sources]
        Aster Data ODBC for nCluster DSN=AsterDriver
        
        [Aster Data ODBC for nCluster DSN]
        Driver=AsterDriver
        SERVER=192.168.100.100
        PORT=2406
        DATABASE=beehive
        # UID=beehive
        # PWD=beehive
        NumericAndDecimalAsDouble=1

    do the same for **/etc/odbcinst.ini**:

        [AsterDriver]
        Driver=/usr/local/lib/stage/clients-odbc-linux64/unixODBC/lib/libAsterDriver.so
        IconvEncoding=UCS-4LE

4. Download the project into a Virtual Host of Apache.
5. Download and install [composer](https://getcomposer.org/):

        sudo apt-get install composer

    and run

        composer install

6. Rename the **config.sample.php** file to **config.php** and edit it at your needs.
7. Setup is complete!