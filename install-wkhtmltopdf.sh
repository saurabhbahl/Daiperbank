cd ~
wget https://downloads.wkhtmltopdf.org/0.12/0.12.3/wkhtmltox-0.12.3_linux-generic-amd64.tar.xz
tar xvf wkhtmltox-0.12.3_linux-generic-amd64.tar.xz
mv wkhtmltox/bin/wkhtmlto* /usr/bin
apt-get install -y openssl build-essential libssl-dev libxrender-dev git-core libx11-dev libxext-dev libfontconfig1-dev libfreetype6-dev fontconfig