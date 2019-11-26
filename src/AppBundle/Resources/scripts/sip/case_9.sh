#!/bin/sh

curl -v \
    -H "Content-Type: application/json" \
    --data '{"action":"sipstatus"}' \
    http://localhost:8081/account.php

# < HTTP/1.0 404 Not Found
# < Date: Tue, 26 Nov 2019 20:14:05 GMT
# < Server: Apache/2.4.6 (CentOS) PHP/5.4.16
# < X-Powered-By: PHP/5.4.16
# < Content-Length: 39
# < Connection: close
# < Content-Type: application/json
# < 
# * Curl_http_done: called premature == 0
# * Closing connection 0
# {"code":246,"errors":"login not exist"}
