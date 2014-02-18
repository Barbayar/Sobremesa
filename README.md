## Sobremesa
Sobremesa is a small social lunch service. You can use it in your company or organization to help people to socialize.

Documentation: http://sobremesa.barbayar.net/documentation

## Using
##### 1. Download
`git clone git@github.com:Barbayar/Sobremesa.git`<br>
or `git clone https://github.com/Barbayar/Sobremesa.git`

##### 2. Install
```
cd Sobremesa
./bin/install.sh
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
