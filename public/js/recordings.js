/*
*   Date: 09-06-2021
*   Author: Iyad Al-Kassab @ SKITSC
*   Description: js for recordings page
*/

// initial offset is 0, starts at recording 1
const recordings_per_page = 50;

let g_recordings_offset = 0;
let g_current_page = 1;

var current_min_index = 0;
var current_max_index = recordings_per_page;

var index_min_element = document.getElementById("index-min");
var index_max_element = document.getElementById("index-max");

document.addEventListener("DOMContentLoaded", (event) => {

    var date_element = document.getElementById("date-search-id");
    date_element.value = new Date().toDateInputValue();

    fetch_data("utils/utils_recordings.php?data_required=total_calls_number", "total_calls_number", true, function(response) {

        var total_calls_number_element = document.getElementById("total_calls_number");
        total_calls_number_element.innerHTML = response;
        
        calculate_pagination();
    });

    fetch_data("utils/list_recordings.php", "app-recordings-table", true, function(response) {

        var loader_row = document.getElementById("loader-row");
        loader_row.remove();

        var recordings_table = document.getElementById("app-recordings-table").tBodies[0];
        recordings_table.innerHTML += response;
    });
});

Date.prototype.toDateInputValue = (function() {
    
    var local = new Date(this);
    local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
    return local.toJSON().slice(0,10);
});

function get_total_recordings() {

    var total_calls_number_element = document.getElementById("total_calls_number");
    return parseInt(total_calls_number_element.innerHTML);
}

function calculate_pagination() {

    var total_calls_number = get_total_recordings();

    current_min_index = (g_current_page - 1) * 50 + 1;
    current_max_index = (g_current_page * 50);
    
    if (total_calls_number == 0) {
        document.getElementById("nav-item").innerHTML = "no recordings...";
    } else if (total_calls_number < current_max_index) {
        current_max_index = total_calls_number;
        index_min_element.innerHTML = current_min_index;
        index_max_element.innerHTML = total_calls_number;
    } else {
        index_min_element.innerHTML = current_min_index;
        index_max_element.innerHTML = current_max_index;
    }

    console.log("current_page: " + g_current_page);
    console.log("index_min: " + current_min_index);
    console.log("index max:" + current_max_index);
}

async function fetch_data(endpoint, elementid, bool_async, callback) {

    var xhttp = new XMLHttpRequest();
  
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            callback(this.responseText);
        }
    };
    xhttp.open("GET", endpoint, bool_async); //async since we need to obtain the value
    xhttp.send();
}

function reset_table() {

    var empty_table = '<table class="app-recordings-table" id="app-recordings-table"><tr><th class="table-recordings-cell col-title col-call-number"></th><th class="table-recordings-cell col-title col-call-uuid">UUID</th><th class="table-recordings-cell col-title col-call-time">Time</th><th class="table-recordings-cell col-title col-call-duration">Duration</th><th class="table-recordings-cell col-title col-call-from">From</th><th class="table-recordings-cell col-title col-call-to">To</th></tr><tr id="loader-row"><td colspan="6"><div class="recordings-loader"><div class="lds-grid"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></td></tr></table>';

    var recordings_table = document.getElementById("id-recording-table");
    recordings_table.innerHTML = empty_table;

    // disable nav buttons
    document.getElementById("go-left").disabled = true;
    document.getElementById("go-right").disabled = true;
}

function restore_table() {
    var xhttp = new XMLHttpRequest();
  
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var loader_row = document.getElementById("loader-row");
            loader_row.remove();
    
            var recordings_table = document.getElementById("app-recordings-table").tBodies[0];
            recordings_table.innerHTML += this.responseText;

            document.getElementById("go-left").disabled = false;
            document.getElementById("go-right").disabled = false;
        }
    };
    xhttp.open("GET", "utils/list_recordings.php?page=" + g_current_page);
    xhttp.send();  
}

function next_page() {

    var max_page = Math.ceil(get_total_recordings() / recordings_per_page);
    console.log("MAX PAGE:" + max_page);

    if (g_current_page < max_page) {

        reset_table();

        g_current_page++;
        calculate_pagination();

        restore_table();
    }
}

function previous_page() {
    var max_page = Math.ceil(get_total_recordings() / recordings_per_page);
    console.log("MAX PAGE:" + max_page);

    if (g_current_page > 1) {

        reset_table();

        g_current_page--;
        calculate_pagination();

        restore_table();
    }
}

function search_phone() {

    reset_table();

    var search = document.getElementById("from-to-search-from").value;

    var xhttp = new XMLHttpRequest();
  
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var loader_row = document.getElementById("loader-row");
            loader_row.remove();
    
            var recordings_table = document.getElementById("app-recordings-table").tBodies[0];
            recordings_table.innerHTML += this.responseText;

            document.getElementById("go-left").disabled = false;
            document.getElementById("go-right").disabled = false;
        }
    };
    xhttp.open("GET", "utils/list_recordings.php?search_phone=" + search + "&page=" + g_current_page);
    xhttp.send();  
}