var ajax = {
    defaultContentType: 'application/x-www-form-urlencoded',
    fileUpload: false,
};

ajax.send = function(url, callback, method, data) {
    var xhr = new XMLHttpRequest();
//    var xhr = ajax.xhr;
    xhr.open(method, url, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
            if (xhr.responseType !== "") {
                callback(xhr.response);
            } else {
                callback(xhr.responseText);
            }
        }
    };
    if (method == 'POST' && !ajax.fileUpload) {
        xhr.setRequestHeader('Content-type', ajax.defaultContentType);
    }
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.send(data);
    ajax.fileUpload = false;
};

ajax.get = function(url, data, callback) {
    var query = [];
    for (var key in data) {
        if (key === '' || data[key] === '') {
            continue;
        }
        query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
    }
    ajax.send(url + (query.length !== 0 ? '?'+query.join('&') : ''), callback, 'GET', null)
};

ajax.post = function(url, data, callback) {
    try {
    var query = [];
    var radio = [];

    if (data.toString() == "[object Object]") {
        for (var key in data) {
            query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
        }
    } else {
        //form elements
        var activator = document.activeElement.hasAttribute('type') ? document.activeElement.getAttribute('type') : false;
        var submits = [];
        for (var i = 0; i < data.length; i++) {
            //check existing radio
            switch (data[i].type) {
                case "select":
                case "select-multiple":
//                    var name = data[i].name.slice(0,-2);
                    for (var j=0, len=data[i].options.length; j<len; j++) {
                        // check if selected
                        if (data[i].options[j].selected) {
                            // add to array of option elements to return from this function
                            query.push(encodeURIComponent(data[i].name) + '=' + data[i].options[j].value);
                        }
                    }
                    break;
                case "radio":
                    if (data[i].checked) {
                        query.push(encodeURIComponent(data[i].name) + '=' + data[i].value);
                    }
                    break;
                case "checkbox":
                    if (data[i].checked) {
                        query.push(encodeURIComponent(data[i].name) + '=' + data[i].value || 'on');
                    }
                    break;
                case "submit":
                    submits.push(data[i]);
                    break;
                case "file":
                    if (window.FormData === undefined) return;
                    var formData = new FormData();
                        for (var j = 0; j< data[i].files.length; j++) {
                            var file = data[i].files[j];
                            //debug file upload :)
                            console.log("file name: " +data[i].name);
                            formData.append(data[i].name, file, file.name);
                        }
                    ajax.fileUpload = true;
                    ajax.send(url, callback, 'POST', formData);
                    break;
                default:
                    var value = encodeURIComponent(data[i].value);
                    query.push(encodeURIComponent(data[i].name) + '=' + value);
            }
        }
        if (activator) {
            if (activator === 'submit') {
                query.push(document.activeElement.name + '=' + document.activeElement.value);
            } else {
                if (submits.length > 0) {
                    query.push(submits[0].name + '=' + submits[0].value);
                }
            }
        }
    }
    if (query.length === 0 || query.toString() === '=') return;
    ajax.send(url, callback, 'POST', query.join('&'))
    } catch(e) {
        display(e.message);
    }
};


/* callback function to display server response */
function display(data, time) {
    if (time === undefined) {
        time = 3000;
    }
    // need to have element with ID=#status
    var st = document.querySelector("#status");
    st.innerHTML = data;
    st.style.display = "block";
    setTimeout(function() {
        st = document.querySelector("#status");
        st.innerHTML = "";
        st.style.display = "none";

    }, time);
}


function postForm(event) {
	var f = this;
	var callback = this.getAttribute('data-callback');
	event.stopImmediatePropagation();
	'use strict'
	var submitBtn = null;
	event.preventDefault(); //stop sending form
	event.stopPropagation();
	if (f.className.indexOf('sending') > -1) {
		return;     //form is already submitting
	} else {
		f.className += ' sending';
		var clicked = document.activeElement;
//                if (clicked.type === 'BUTTON') return false;
		var go = false;

		console.log(clicked.type);

		if (clicked.type === 'submit') {
			go = true;
		} else {
			clicked = f.querySelector('[type=submit]');
			if (clicked !== null) {
				go = true;
			}
		}
		if (go) {
			submitBtn = {e: clicked, html: clicked.innerHTML, className: clicked.className};
			clicked.style.width = clicked.getBoundingClientRect().width + 'px';
			clicked.innerHTML = '&nbsp;<img src="/assets/icons/loading.gif" class="loader-indicator">&nbsp;';
		}

	}
	//NOTE error clearing commented for now as 11.10.2016
//	clearFormErrors(f.querySelectorAll(".error"));
	var el = f.elements;
	var loader = f.querySelector(".loader") || false;
	if (loader) loader.style.display = "inline-block";

	var actionURL = f.getAttribute('action');
	var method = f.getAttribute('method') || 'post';
	method = method.toLowerCase();
	ajax[method](actionURL, el, function(data) {
		if (submitBtn) {
			submitBtn.e.innerHTML = submitBtn.html;
			submitBtn = null;
		}
        //remove sending class
        f.className = f.className.replace( 'sending', '');
        if (callback) {
            callback(data);
        } else {
            try {
                if (data[0] !== '{') {
                    ajaxLink({href: data, offline: true});
                    return;
                }
                var response = JSON.parse(data);
                if (response.success === true) {
                    if (response.hasOwnProperty("redirect")) {
                        window.location = response.redirect;
                    }
                    //custom popup response
                    if (response.hasOwnProperty("popup")) {
                        var hrefClass = response.class || "large-form";
                        var padding = response.padding || "";
                        ajaxLink({class: hrefClass, href:response.popup, paddingTop: padding, offline: true});
                    } else {
                        if (response.hasOwnProperty("replace")) {
                            var elClass = response.class || "text-center";
                            f.parentNode.className += ' ' + elClass;
                            f.parentNode.innerHTML = response.replace;
                        } else {
                            if (response.hasOwnProperty("display")) {
                                display(response.display);
                            }
                            //reset form values
                            f.reset();
                        }
                    }
                } else {
                    //display errors
                    console.log('errors');
                    if (response.hasOwnProperty("form")) {
                        markFormErrors(f, response.form);
                    }
                    if (response.hasOwnProperty("popup")) {
//                        alertify.alert(response.popup, function(){}, response.status);
                        alert(response.popup);
                    }
                }
            } catch(e) {
                console.log("Exception parsing JSON: " + e.message);
                console.log(response);
                try {
                    display(data);
                } catch(e) {
                    console.log("Display(data) Exception : " + e.message);
                    alert("Stala sa neočakávaná chyba. Skúste neskôr, alebo nás kontaktujte telefonicky");
                }
            }
        }
    });
    return false;
}

function ajaxLink() {
	alert('ajaxLink function call');
}

document.addEventListener('DOMContentLoaded', function() {
	console.log('Document Ready!');
	var ajaxLinks = document.getElementsByClassName('ajax');
	for (var i=0,l=ajaxLinks.length; i<l; i++) {
		//postForm binding
		if (ajaxLinks[i].tagName.toLowerCase() === 'form') {
			ajaxLinks[i].addEventListener('submit', postForm);
		} else {
			//ajaxLink binding
			ajaxLinks[i].addEventListener('submit', ajaxLink);
		}
	}
})
