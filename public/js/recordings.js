/*
*   Date: 09-06-2021
*   Author: Iyad Al-Kassab @ SKITSC
*   Description: js for recordings page
*/

// initial offset is 0, starts at recording 1
const recordings_per_page = 50;

let g_recordings_offset = 0;

document.addEventListener("DOMContentLoaded", (event) => {

    var date_element = document.getElementById("date-search-id");
    date_element.value = new Date().toDateInputValue();

    var current_min_index = 0;
    var current_max_index = recordings_per_page;

    var index_min_element = document.getElementById("index-min");
    var index_max_element = document.getElementById("index-max");

    fetch_dashboard_data("utils/utils_recordings.php?data_required=total_calls_number", "total_calls_number", true, function(response) {

        var total_calls_number_element = document.getElementById("total_calls_number");
        total_calls_number_element.innerHTML = response;
        var total_calls_number = response;
        
        if (total_calls_number == 0) {
            document.getElementById("nav-item").innerHTML = "no recordings...";
        } else if (total_calls_number < current_max_index) {
            index_max_element.innerHTML = total_calls_number;
        } else {
            index_max_element.innerHTML = current_max_index;
        }
    });
});

Date.prototype.toDateInputValue = (function() {
    var local = new Date(this);
    local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
    return local.toJSON().slice(0,10);
});

function calculate_pagination() {


}

async function fetch_dashboard_data(endpoint, elementid, bool_async, callback) {

    var xhttp = new XMLHttpRequest();
  
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        callback(this.responseText);
      }
    };
    xhttp.open("GET", endpoint, bool_async); //async since we need to obtain the value
    xhttp.send();
}