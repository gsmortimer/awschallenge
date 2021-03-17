/* 
 *   
 *    myip.js - By George Mortimer
 *    version 0.1
 *    17 MAR 2021
 *
 *
 */

/*******  CONFIG  *******/

var ipURL = "php/myip.php";
var whoisURL = "php/whois.php";
var vtURL = "php/vt.php";

var ID_ip = "myIP";
var ID_whois = "whois";
var ID_vt = "virusTotal";
var ID_scroll = "moreInfo";

/************************/


// New Primary data fetch routine, pinched from a previous project of mine
// Uses Promises to allow completion of request before further processing data
// appends a random GET argument to prevent browser caching of response
// Othewise, it's a textbook Asynchronous HTTP request.
// "src" is the URL to query
function fetch_data (src) {
	return new Promise(function (resolve, reject) {
		var request = new XMLHttpRequest();
		request.onreadystatechange = function () {
			if (request.readyState !== 4) return;
			if (request.status >= 200 && request.status < 300) {
				resolve(request);
			} else {
				reject({
					status: request.status,
					statusText: request.statusText
				});
			}
		};
		request.open('GET',  src + "?" + Math.random(), true);
		request.send();
	}).catch(function(data) {
		console.error ("fetch_data: " + src + " failed to load");

	});
};

// 
//Messy function to pull the relevant JSON fields.
//The additional fields could be extracted directly from the server response, but they aren't that interesting

function display_whois(id, content) {
        try {
                json = JSON.parse(content)
                var whois = "Country: <br><div class='lead'>" + json.regrinfo.network.country + "</div><br>\n";
                whois += "IP Range: <br><div class='lead'>" + json.regrinfo.network.inetnum + "</div><br>\n";
                whois += "Organisation: <br><div class='lead'>" + json.regrinfo.owner.organization + "</div><br>\n";
                whois += "Address:  <br><div class='lead'>" + json.regrinfo.tech.address + "</div><br>\n";
                whois += "Registered?:  <br><div class='lead'>" + json.regrinfo.registered + "</div><br>\n";
        } catch (e) {
                whois = "Whois Lookup failed";
        }
        document.getElementById(id).innerHTML = whois;
}


//Function to parse the JSON data from the VirusTotal API
//Counts the number of comments and displays each one.
function display_vt(id, content) {
        try {
                json = JSON.parse(content);
                console.log(json);
                count = json.length; //count how many entries are in the JSON array
                var vt = "";
                var i;
                if (count == 0 || count == null) {
                        vt = "No VirusTotal Resolution Entries for IP";
                } else {
                        for (i=0; i < count; i++) {
                                vt += "Resolution " 
                                        + (i+1) 
                                        + ": <br><div class='lead'>" 
                                        + json[i].attributes.host_name 
                                        + "</div><br>\n";
                        }
                }
        } catch (e) {
                console.log("Error " + e + "Parsing VT JSON");
                vt = "Error retrieving Data";
        } 

        document.getElementById(id).innerHTML = vt;
}

//Asynchronous load of IP address, overwriting element id "myIP" with response
function displayIP() {
	return new Promise(function (resolve, reject) {
		fetch_data(ipURL)
			.then(function(data) {
				document.getElementById(ID_ip).innerHTML = data.responseText;
				resolve();
			}).catch(function(data) {
				console.warn ("Error Fetching IP Address");
			});
	});

}

//Asynchronous load of whois data, followed by VirusTotal API data
function moreInfo() {
        return new Promise(function (resolve, reject) {
                fetch_data(whoisURL)
                        .then(function(data) {
                                display_whois(ID_whois,data.responseText)
                                fetch_data(vtURL)
                                        .then(function(data) {
                                                display_vt(ID_vt,data.responseText);
                                                var elmnt = document.getElementById(ID_scroll);
                                                elmnt.scrollIntoView();
                                                resolve();
                                        }).catch(function(data) {
                                                console.warn ("Error showing VirusTotal info");
                                        });
                        }).catch(function(data) {
                                console.warn ("Error showing Whois info");
                        });
        });
}

//run when this script is loaded in
displayIP();   

//On Back a page
window.onpopstate = displayIP();

