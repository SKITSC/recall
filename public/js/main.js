/* distributed file for client js */

var total_calls_global = 0;

document.addEventListener("DOMContentLoaded", (event) => {

    load_total_calls_plivo();
    load_total_size_recordings();
});

    /* responsive hamburger menu */

function toggle_menu() {

    var toggle_element = document.getElementById("toggle-menu");
    if (toggle_element.className === "menu-nav") {
        toggle_element.className += " responsive";
    } else {
        toggle_element.className = "menu-nav";
    }
}

function load_total_calls_plivo() {

    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        total_calls_global = this.responseText;
        document.getElementById("total-calls-plivo-cloud").innerHTML = total_calls_global;
      }
    };
    xhttp.open("GET", "utils/total_recordings.php", true);
    xhttp.send();
}

function load_total_size_recordings() {

    var xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("total-size-recordings").innerHTML = this.responseText;
      }
    };
    xhttp.open("GET", "utils/size_recordings.php", true);
    xhttp.send();
}

var w;

function sync_worker() {

  if (typeof(Worker) !== "undefined") {
    if (typeof(w) == "undefined") {
      w = new Worker("static/js/sync-worker.min.js");
    }
    w.onmessage = function(event) {
      document.getElementById("sync-recordings").innerHTML = event.data;
    };
  } else {
    document.getElementById("sync-recordings").innerHTML = "Sorry! No Web Worker support.";
  }
}