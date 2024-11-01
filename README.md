Author: Diego Lopes da Cruz - diego.lopes@gmail.com

Donations:
XMR: 42k1VqGLnaBQ7QtphE9HJDeGiYbJ6bWJ5N4By7HV1mMBERHxAVdJoYtPYdoiDDL9yNCoN5iwUnXx7TQk7vJiB38C4ukiJL4

# DHCPWebAdmin
Web based isc-dhcp-server administration. <br>
Add, alter and remove many "pools" in /etc/dhcp/dhcpd.conf with PHP. 
 
# Pre-requisites
GNU/Linux (tested on Debian Strecth 9.4) <br>
Apache 2.4 <br>
PHP 7.0 <br>
libapache2-mod-php7.0 <br>
PHP7.0-mysql module <br>
Mysql server 5.5 <br>
Expect 5.45   

# Installation
Copy all files .php, css and javascript directories to your server web directory.
Create a database such as dump file "db.sql" structure on mysql-server.

# Default user
The deafult user is "admin" and password "admin"
To change the password, edit the "settings.php" file and alter the line 29 with new password in md5 format.


Enjoy.
