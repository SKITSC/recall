var total_calls_global = 0;

var xhttp1 = new XMLHttpRequest();

xhttp1.onreadystatechange = function() {
  if (this.readyState == 4 && this.status == 200) {
    total_calls_global = this.responseText;
  }
};
xhttp1.open("GET", "../../utils/total_recordings.php", false);
xhttp1.send();

postMessage("Syncing...");

var xhttp2 = new XMLHttpRequest();
xhttp2.open("GET", "../../utils/fetch_recordings.php?fetch=" + 20, false);
xhttp2.send();

postMessage("Synced, Downloading...")

var xhttp3 = new XMLHttpRequest();
xhttp3.open("GET", "../../utils/download_recordings.php", false);
xhttp3.send();

postMessage("Up to date...");