## slunch
slunch is a small social lunch service. You can use it in your company or organization to help people to socialize.

## Using
##### 1. Download
`git clone git@github.com:Barbayar/slunch.git`<br>
or `git clone https://github.com/Barbayar/slunch.git`

##### 2. Install
```
cd slunch
./bin/install.sh
```

##### 3. Configure HTTP Server
If you want to use the documentation to naivigate APIs, configure URLs like mentioned above<br>
http://your.domain ⇔ slunch/htdocs<br>
http://your.domain/api ⇔ slunch/htdocs/api<br>
http://your.domain/documentation ⇔ slunch/htdocs/documentation<br>
otherwise<br>
http://your.domain/configure/whatever/you/like ⇔ slunch/htdocs<br>

##### 4. Implement SLAuthentication.php
Implement your own authentication method using `SLAuthentication.php.sample`. If your company uses LDAP for authentication, you can use `SLAuthentication.php.ldap`.

##### 5. Implement SLNotification.php
Implement your own notification module using `SLNotiification.php.sample`.

## Contributing
I'm preparing the contributing guidelines, so please wait for a while.
