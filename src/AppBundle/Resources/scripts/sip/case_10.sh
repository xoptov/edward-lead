#!/bin/sh

curl -v \
    -H "Content-Type: application/json" \
    --data '{"action":"sipstatus","login":"1234"}' \
    http://localhost:8081/account.php

# < HTTP/1.1 200 OK
# < Date: Tue, 26 Nov 2019 20:16:35 GMT
# < Server: Apache/2.4.6 (CentOS) PHP/5.4.16
# < X-Powered-By: PHP/5.4.16
# < Content-Length: 36
# < Content-Type: application/json
# < 
# * Curl_http_done: called premature == 0
# * Connection #0 to host localhost left intact
# {"code":3,"message":"not connected"}
