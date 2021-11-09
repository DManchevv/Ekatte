function load() {
    let searchBar = document.getElementById("findSettlement");

    searchBar.addEventListener('input', () => {
        generateAutocomplete(searchBar);
    });
}

function generateAutocomplete(searchBar) {
    let xhr = new XMLHttpRequest();
    let response;
    xhr.open("POST", "../PHP/autocomplete.php", true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            response = JSON.parse(this.responseText);
            let suggestions = response.data;
            
            if (!suggestions) {
                return;
            }

            let a, b, val = searchBar.value;
            closeAllLists();
            currentFocus = -1;
            a = document.createElement("DIV");
            a.setAttribute("id", searchBar.id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items");
            searchBar.parentNode.appendChild(a);
            if (!val) {
                for (let i = 0; i < suggestions.length; i++) {
                b = document.createElement("DIV");
                b.innerHTML = "<strong>" + suggestions[i]["name"] + "</strong>";
                b.innerHTML += "<input type='hidden' value='" + suggestions[i]["name"] + "'>";
                b.addEventListener("click", function(e) {
                    searchBar.value = e.target.innerText;
                }
                                    );
                a.appendChild(b);
                }
            }
            else {
                for (let i = 0; i < suggestions.length; i++) {
                    if (suggestions[i]["name"].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                        b = document.createElement("DIV");
                        b.innerHTML = "<strong>" + suggestions[i]["name"] + "</strong>";
                        b.innerHTML += "<input type='hidden' value='" + suggestions[i]["name"] + "'>";
                        b.addEventListener("click", function(e) {
                            searchBar.value = e.target.innerText;
                        }
                                        );
                        a.appendChild(b);
                    }
                }
            }
        }
    };

    xhr.send(JSON.stringify({"name" : searchBar.value+'%'}));
}

function closeAllLists(textField) {
    let x = document.getElementsByClassName("autocomplete-items");
    for (let i = 0; i < x.length; i++) {
      if (textField != x[i]) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
}

document.addEventListener("click", function (e) {
    closeAllLists(e.target);
});

window.onload = load;