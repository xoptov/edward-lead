#!/bin/sh

curl -v \
    -H "Content-Type: application/json" \
    --data '{"login":"1"}' \
    http://localhost:8081/account.php

# < HTTP/1.1 400 Bad Request
# < Date: Tue, 26 Nov 2019 19:58:20 GMT
# < Server: Apache/2.4.6 (CentOS) PHP/5.4.16
# < X-Powered-By: PHP/5.4.16
# < Content-Length: 33
# < Connection: close
# < Content-Type: application/json
# < 
# * Curl_http_done: called premature == 0
# * Closing connection 0
# {"code":252,"errors":"no action"}
