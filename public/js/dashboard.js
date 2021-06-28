/*
*   Date: 09-06-2021
*   Author: Iyad Al-Kassab @ SKITSC
*   Description: js for dashboard page
*   Copyright (C) 2021 Iyad Al-Kassab
*/

document.addEventListener("DOMContentLoaded", (event) => {

    fetch_dashboard_data("utils/total_recordings.php", "total-calls-plivo-cloud");
    fetch_dashboard_data("utils/size_recordings.php", "total-size-recordings");
    fetch_dashboard_data("utils/utils_recordings.php?data_required=most_dialed_number", "most-dialed-number");
    fetch_dashboard_data("utils/utils_recordings.php?data_required=most_calling_number", "most-calling-number");
    fetch_dashboard_data("utils/utils_recordings.php?data_required=shortest_call", "shortest-call");
    fetch_dashboard_data("utils/utils_recordings.php?data_required=longuest_call", "longuest-call");
});

function fetch_dashboard_data(endpoint, elementid) {

    var xhttp = new XMLHttpRequest();
  
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById(elementid).innerHTML = this.responseText;
      }
    };
    xhttp.open("GET", endpoint, true);
    xhttp.send();
  }
  
  // web worker
  
  var w;
  
  function sync_worker() {
  
    if (typeof(Worker) !== "undefined") {
      if (typeof(w) == "undefined") {
        w = new Worker("static/js/sync-worker.min.js");
      }
      w.onmessage = function(event) {
        var pop_element = document.getElementById("sync-recordings");
        pop_element.innerHTML = event.data;
        pop_element.style.opacity = 1;
        setTimeout(function() {pop_element.style.opacity = 0}, 2000);
      };
    } else {
      document.getElementById("sync-recordings").innerHTML = "Sorry! No Web Worker support.";
    }
  }