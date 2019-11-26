#!/bin/sh

curl -v \
    -H "Content-Type: application/json" \
    --data '{"action":"addsip","login":"1234","password":"12345678"}' \
    http://localhost:8081/account.php

# < HTTP/1.1 200 OK
# < Date: Tue, 26 Nov 2019 20:06:23 GMT
# < Server: Apache/2.4.6 (CentOS) PHP/5.4.16
# < X-Powered-By: PHP/5.4.16
# < Content-Length: 36
# < Content-Type: application/json
# < 
# * Curl_http_done: called premature == 0
# * Connection #0 to host localhost left intact
# {"code":1,"message":"login created"}

# < HTTP/1.1 400 Bad Request
# < Date: Tue, 26 Nov 2019 20:06:56 GMT
# < Server: Apache/2.4.6 (CentOS) PHP/5.4.16
# < X-Powered-By: PHP/5.4.16
# < Content-Length: 35
# < Connection: close
# < Content-Type: application/json
# < 
# * Curl_http_done: called premature == 0
# * Closing connection 0
# {"code":249,"errors":"login exist"}
