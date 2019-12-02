ready(function(){

    runEsputnik()

    var DBOpenRequest = window.indexedDB.open("es_push_integration", 4);


});


var DBOpenRequest = window.indexedDB.open("toDoList", 4);

DBOpenRequest.onsuccess = function(event) {

    db = DBOpenRequest.result;
    console.log(db)
};
//
// function getData() {
//     // open a read/write db transaction, ready for retrieving the data
//     var transaction = db.transaction(["toDoList"], "readwrite");
//
//     // report on the success of the transaction completing, when everything is done
//     transaction.oncomplete = function(event) {
//         note.innerHTML += '<li>Transaction completed.</li>';
//     };
//
//     transaction.onerror = function(event) {
//         note.innerHTML += '<li>Transaction not opened due to error: ' + transaction.error + '</li>';
//     };
//
//     // create an object store on the transaction
//     var objectStore = transaction.objectStore("toDoList");
//
//     // Make a request to get a record by key from the object store
//     var objectStoreRequest = objectStore.get("Walk dog");
//
//     objectStoreRequest.onsuccess = function(event) {
//         // report the success of our request
//         note.innerHTML += '<li>Request successful.</li>';
//
//         var myRecord = objectStoreRequest.result;
//     };
//
// };


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

// TODO remove
function runEsputnik() {

    (function(i,s,o,g,r,a,m){
            i["esSdk"] = r;
            i[r] = i[r] || function() {
                (i[r].q = i[r].q || []).push(arguments)
            }, a=s.createElement(o), m=s.getElementsByTagName(o)[0]; a.async=1; a.src=g;
            m.parentNode.insertBefore(a,m)}
    ) (window, document, "script", "https://esputnik.com/scripts/v1/public/scripts?apiKey=eyJhbGciOiJSUzI1NiJ9.eyJzdWIiOiI0NTI0ZWZhYTJkYzI2MGRmYTM4YTE1NDBlMWFhYjE0N2Q1OTAzNzBiM2Y0Zjk3ODQwYmE4MGU2ZGM3YzEwMDRmMjJhOGU1MzE1ZmJlYTIyZTBhMDMzY2FhODQ3Yjg2NTQ1MGFhYTM0NjEwNjUzNGMxZTcyMjRhOTQ2NjVmYmM0NGJkZDhlYjZkNmIyMDc0NWFhNDY0YjcyNWIzODg0YjEyMDI4ZGVjOTY5YTU3In0.we1MGdYaaQ-OGYw9cdmvx-pc6s4ILdtXcIN3BrShFUS-VABz37Xrn2gQp8Affq-u0qxg8w51T0MGZtzhEsntZg&domain=24A4E9F1-04AB-4E99-9D21-E5D12739B62F", "es");
    es("pushOn")

}