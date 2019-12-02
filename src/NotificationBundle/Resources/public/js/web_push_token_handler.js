ready(function(){

    (async () => {

        const dbName = 'es-push-integration'
        const storeName = 'integration-data'
        const key = 'SID'
        const version = 1

        const db = await openDB(dbName, version)
        const item = await db.transaction(storeName).objectStore(storeName).get(key)

        item.onsuccess = (e) => {

            const token = e.target.result.value.sid;

            if(isTokenExpired()){

                var xhr = new XMLHttpRequest();
                var url = "/api/notifications/push";
                xhr.open("POST", url, true);
                xhr.setRequestHeader("Content-Type", "application/json");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {

                        setExpiration()

                    }
                };

                xhr.onerror = function () {
                    console.error('Some error when sending web push token to server')
                }

                var data = JSON.stringify({token});
                xhr.send(data);

            }

        }

        item.onerror = (e) => {
            console.error('Some error when getting web push token from IndexDb')
        }

    })()

});


function ready(callback){
    // in case the document is already rendered
    if (document.readyState!='loading') callback();
    // modern browsers
    else if (document.addEventListener) document.addEventListener('DOMContentLoaded', callback);
    // IE <= 8
    else document.attachEvent('onreadystatechange', function(){
            if (document.readyState=='complete') callback();
        });
}

//////// TOKEN EXPIRATION /////////

function isTokenExpired() {

    var date = new Date().toLocaleDateString();

    if( localStorage.WEB_PUSH_TOKEN_DATE_SAT == date )
        return false;

    return true;
}

function setExpiration() {

    localStorage.WEB_PUSH_TOKEN_DATE_SAT = new Date().toLocaleDateString()
}

//////// TOKEN EXPIRATION /////////


//////// DB /////////

function openDB(name, version, { blocked, upgrade, blocking } = {}) {
    var request = indexedDB.open(name, version);
    if (upgrade) {
        request.addEventListener('upgradeneeded', event => {
            upgrade(wrap(request.result), event.oldVersion, event.newVersion, wrap(request.transaction));
        });
    }

    return  wrap(request);
}

function promisifyRequest(request) {
    const promise = new Promise((resolve, reject) => {
        const unlisten = () => {
            request.removeEventListener('success', success);
            request.removeEventListener('error', error);
        };
        const success = () => {
            resolve(wrap(request.result));
            unlisten();
        };
        const error = () => {
            reject(request.error);
            unlisten();
        };
        request.addEventListener('success', success);
        request.addEventListener('error', error);
    });
    promise.
    then(value => {
        // Since cursoring reuses the IDBRequest (*sigh*), we cache it for later retrieval
        // (see wrapFunction).
        if (value instanceof IDBCursor) {
            cursorRequestMap.set(value, request);
        }
        // Catching to avoid "Uncaught Promise exceptions"
    }).
    catch(() => {});

    return promise;
}


function wrap(value) {

    if (value instanceof IDBRequest)
        return promisifyRequest(value);

    return value;
}

//////// DB /////////