# awschallenge
AWS Challenge

My solution to the "AWS Challenge"

Installation (shown for a new debian distro):

Set up a Webserver with php.
sudo apt update
sudo apt install nginx php-fom


Extract files into your chosen www folder
git clone --depth=1 https://github.com/gsmortimer/awschallenge.git

Install phpwhois.org library:
cd /usr/share/php
sudo git clone --depth=1 https://github.com/sparc/phpWhois.org.git

Install php-curl:
sudo apt install php-curl

Add resources from https://github.com/StartBootstrap/startbootstrap-landing-page
cd ~
git clone --depth=1 https://github.com/StartBootstrap/startbootstrap-landing-page.git
cd ~/startbootstrap-landing-page
sudo cp -r css vendor /var/www/<chosen-www-folder>
  
Modify php/vt.php to add your VirusTotal API Key
