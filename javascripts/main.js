var xmlhttp;

if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp = new XMLHttpRequest();
} else {
    // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
}

xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == 4 ) {
        if(xmlhttp.status == 200){
            document.getElementById('content').innerHTML =
                marked(xmlhttp.responseText);
        }
        else {
            alert('something else other than 200 was returned')
        }
    }
};

xmlhttp.open("GET", "README.md", true);
xmlhttp.send();