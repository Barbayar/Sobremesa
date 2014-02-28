BASEDIR=$(dirname $0)
cd ${BASEDIR}

php ../vendor/zircote/swagger-php/swagger.phar ../api/ -o ../documentation/api-docs/
