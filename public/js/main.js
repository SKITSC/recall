/* distributed file for client js 

*   Copyright (C) 2021 Iyad Al-Kassab
*/

// responsive hamburger menu

function toggle_menu() {

    var toggle_element = document.getElementById("toggle-menu");
    if (toggle_element.className === "menu-nav") {
        toggle_element.className += " responsive";
    } else {
        toggle_element.className = "menu-nav";
    }
}