

function getStatistics() {
    let settlements = document.getElementById("settlements");
    let townHalls = document.getElementById("townHalls");
    let municipalities = document.getElementById("municipalities");
    let areas = document.getElementById("areas");

    let xhr = new XMLHttpRequest();
    let response;
    xhr.open("GET", "../PHP/getStatistics.php", true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            response = JSON.parse(this.responseText);

            settlements.value = response.settlements;
            townHalls.value = response.town_halls;
            municipalities.value = response.municipalities;
            areas.value = response.areas; 
        }
    };

    xhr.send();


}

window.onload = getStatistics;