Listen 8080

<VirtualHost *:8080>
  LoadModule php5_module /home/ubuntu/.phpenv/versions/5.5.7/libexec/libphp5.so

  ServerName dev.rollerderbytestomatic.com
  
  DocumentRoot /home/ubuntu/RDTOM-Source
  DirectoryIndex index.php

  <FilesMatch \.php$>
    SetHandler application/x-httpd-php
  </FilesMatch>

</VirtualHost>