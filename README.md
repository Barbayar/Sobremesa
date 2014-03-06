## Sobremesa
Sobremesa is a small social lunch service. You can use it in your company or organization to help people to socialize.

Demo: http://sobremesa.barbayar.net/<br>
Documentation: http://sobremesa.barbayar.net/documentation

> **sobremesa**<br>
(n.) the time spent around the table after lunch or dinner, talking to the people you shared the meal with; time to digest and savor both food and friendship

## Using
##### 0. Requirements
PHP 5.4.* and above

##### 1. Download
`git clone git@github.com:Barbayar/Sobremesa.git`<br>
or `git clone https://github.com/Barbayar/Sobremesa.git`

##### 2. Install Composer packages
```
cd Sobremesa
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

##### 3. Configure HTTP Server
If you want to use the documentation to naivigate APIs, configure URLs like mentioned above<br>
http://your.domain ⇔ Sobremesa/htdocs<br>
http://your.domain/api ⇔ Sobremesa/htdocs/api<br>
http://your.domain/documentation ⇔ Sobremesa/htdocs/documentation<br>
otherwise<br>
http://your.domain/configure/whatever/you/like ⇔ Sobremesa/htdocs<br>

##### 4. Implement SLAuthentication.php
Implement your own authentication method using `SLAuthentication.php.sample`. If your company uses LDAP for authentication, you can use `SLAuthentication.php.ldap`.

##### 5. Implement SLNotification.php
Implement your own notification module using `SLNotiification.php.sample`.

## Contributing
I'm preparing the contributing guidelines, so please wait for a while.
