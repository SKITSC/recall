/*
*   Date: 09-06-2021
*   Author: Iyad Al-Kassab @ SKITSC
*   Description: webworker that simply fetch the last recordings in the background
*/

// without the timeout it blocks the UI thread
var timeout = 0;

postMessage("Syncing...");

// what can go wrong if you make synchronous ajax calls using jquery, understanding event loops in javascript
setTimeout(function() {

    var xhttp1 = new XMLHttpRequest();
    xhttp1.open("GET", "../../utils/fetch_recordings.php?fetch=20", true);
    xhttp1.send();

    postMessage("Synced, Downloading...");

    var xhttp2 = new XMLHttpRequest();
    xhttp2.open("GET", "../../utils/download_recordings.php", true);
    xhttp2.send();

    postMessage("Up to date...");

}, timeout);