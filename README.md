poprox
======

docker build -t poprox .
docker run -it --rm -p 80:80 --name poprox \
-e FQDN=<hostname> \
-e MEMEXDBHOST=<memex_ht database hostname> \
-e MEMEXDBUSER=<memex_ht database username> \
-e MEMEXDBPASS=<memex_ht database password> \
-e ISTDBHOST=<memex_ist database hostname> \
-e ISTDBUSER=<memex_ist database username> \
-e ISTDBPASS=<memex_ist database password> \
-e WEBAPPDBHOST=<webapp database hostname> \
-e WEBAPPDBUSER=<webapp database username> \
-e WEBAPPDBPASS=<webapp database password> \
poprox /var/www/html/run-poprox

http://<hostname>/poprox
