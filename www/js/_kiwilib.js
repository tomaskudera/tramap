/*------------------ KIWILIB EXCERPT ------------------------------------*/
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
/*********************************************/

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

/* FORM ERROR MARKING FUNCTION */
function markFormErrors(form, errors) {
    for (var i = 0; i < errors.length; i++) {
        if (typeof (document.forms[1].elements[errors[i]]) == 'undefined') {
            form.querySelector('#'+errors[i]).className += ' error';
            continue;
        }
        form.elements[errors[i]].className += " error";
    }

}
/* FORM ERROR CLEANING FUNCTIONS */
function clearFormErrors(arr) {
    for (var i=0; i<arr.length; i++) {
        arr[i].className = arr[i].className.replace('error', '');
    }
}

/**autosize */
!function(e,t){if("function"==typeof define&&define.amd)define(["exports","module"],t);else if("undefined"!=typeof exports&&"undefined"!=typeof module)t(exports,module);else{var o={exports:{}};t(o.exports,o),e.autosize=o.exports}}(this,function(e,t){"use strict";function o(e){function t(){var t=window.getComputedStyle(e,null);"vertical"===t.resize?e.style.resize="none":"both"===t.resize&&(e.style.resize="horizontal"),l="content-box"===t.boxSizing?-(parseFloat(t.paddingTop)+parseFloat(t.paddingBottom)):parseFloat(t.borderTopWidth)+parseFloat(t.borderBottomWidth),n()}function o(t){var o=e.style.width;e.style.width="0px",e.offsetWidth,e.style.width=o,u=t,a&&(e.style.overflowY=t),n()}function n(){var t=e.style.height,n=document.documentElement.scrollTop,i=document.body.scrollTop,r=e.style.height;e.style.height="auto";var d=e.scrollHeight+l;if(0===e.scrollHeight)return void(e.style.height=r);e.style.height=d+"px",document.documentElement.scrollTop=n,document.body.scrollTop=i;var s=window.getComputedStyle(e,null);if(s.height!==e.style.height){if("visible"!==u)return void o("visible")}else if("hidden"!==u)return void o("hidden");if(t!==e.style.height){var a=document.createEvent("Event");a.initEvent("autosize:resized",!0,!1),e.dispatchEvent(a)}}var i=void 0===arguments[1]?{}:arguments[1],r=i.setOverflowX,d=void 0===r?!0:r,s=i.setOverflowY,a=void 0===s?!0:s;if(e&&e.nodeName&&"TEXTAREA"===e.nodeName&&!e.hasAttribute("data-autosize-on")){var l=null,u="hidden",v=function(t){window.removeEventListener("resize",n),e.removeEventListener("input",n),e.removeEventListener("keyup",n),e.removeAttribute("data-autosize-on"),e.removeEventListener("autosize:destroy",v),Object.keys(t).forEach(function(o){e.style[o]=t[o]})}.bind(e,{height:e.style.height,resize:e.style.resize,overflowY:e.style.overflowY,overflowX:e.style.overflowX,wordWrap:e.style.wordWrap});e.addEventListener("autosize:destroy",v),"onpropertychange"in e&&"oninput"in e&&e.addEventListener("keyup",n),window.addEventListener("resize",n),e.addEventListener("input",n),e.addEventListener("autosize:update",n),e.setAttribute("data-autosize-on",!0),a&&(e.style.overflowY="hidden"),d&&(e.style.overflowX="hidden",e.style.wordWrap="break-word"),t()}}function n(e){if(e&&e.nodeName&&"TEXTAREA"===e.nodeName){var t=document.createEvent("Event");t.initEvent("autosize:destroy",!0,!1),e.dispatchEvent(t)}}function i(e){if(e&&e.nodeName&&"TEXTAREA"===e.nodeName){var t=document.createEvent("Event");t.initEvent("autosize:update",!0,!1),e.dispatchEvent(t)}}var r=null;"undefined"==typeof window||"function"!=typeof window.getComputedStyle?(r=function(e){return e},r.destroy=function(e){return e},r.update=function(e){return e}):(r=function(e,t){return e&&Array.prototype.forEach.call(e.length?e:[e],function(e){return o(e,t)}),e},r.destroy=function(e){return e&&Array.prototype.forEach.call(e.length?e:[e],n),e},r.update=function(e){return e&&Array.prototype.forEach.call(e.length?e:[e],i),e}),t.exports=r});

function getHoverDiv(withOverlay) {
    var el = document.createElement("div");
    var y = window.pageYOffset;
    var x_total = document.body.clientWidth;
    var width, x;
    // set width
    if (x_total > 640) {
        width = 640;
    } else {
        if (x_total < 300) {
            width = 300;
        } else {
            width = x_total - 10;
        }
    }
    // set x pos
    x = Math.abs(Math.floor((x_total - width) / 2));
    //el.className = "hover";
    el.className = "hover";
    el.style.overflow = "hidden";
    el.style.top = (y + 10) + "px";
    el.style.left = x + "px";
    el.style.width = width + "px";
    el.style.textAlign = "center";
    el.innerHTML = "<img src='/style/loading.gif' />";
    if (withOverlay) {
        var overlay = document.createElement("div");
        overlay.style.top = 0;
        overlay.style.position = "fixed";
        overlay.style.bottom = 0;
        overlay.style.right = 0;
        overlay.style.left = 0;
        overlay.style.background = "black";
        overlay.style.opacity = "0.8";
        overlay.style.zIndex = 999;
        document.body.appendChild(overlay);
    }
    var removal = function(event) {
        //catch esc
        if (event.keyCode === 27 || event.type === 'click') {
            document.body.removeChild(el);
            withOverlay ? document.body.removeChild(overlay) : "";
            window.removeEventListener('keyup', removal);
        }
    }
    el.onclick = removal;
    withOverlay ? overlay.onclick = removal : "";
    window.addEventListener('keyup', removal);

    return el;
}

function ajaxLink(a, chainedFunction) {
    var submitBtn = null;
    if (a.type === "click") {
        a.preventDefault();
        a = a.target;
    }
    var popup = document.querySelector("#center-container");
    var btn = popup.firstElementChild.firstElementChild;
    if (popup.style.display === "block") {
        btn.click();
    }

    var href = a.href || a.src || null;
    if (href === null) {
        href = a.getAttribute('data-src');
        submitBtn = {e: a, html: a.innerHTML, className: a.className};
        a.style.width = a.getBoundingClientRect().width + 'px';
        a.innerHTML = '&nbsp;<img src="/public/assets/icons/loading.gif" class="loader-indicator">&nbsp;';
    }
    var isImage = ((href.indexOf(".jpg") > -1) || (href.indexOf(".png")) > -1) ? true : false;
    var contentWrap = popup.firstElementChild;
    var content = contentWrap.lastElementChild.firstElementChild;

    popup.style.display = "block";
    //display
    if (href.indexOf('/produkt/') > -1 || a.width) {
        if (a.width) {
            popup.firstElementChild.style.maxWidth = a.width + 'px';
        } else {
            popup.firstElementChild.style.maxWidth = "850px";
        }
    } else {
        popup.firstElementChild.style.maxWidth = "";
    }
    contentWrap.className += ' fade-text';

    var resizer = function() {
        updatePopupSize();
    }

    var displayFunction = function(data) {
        contentWrap.style.width = "95%";
        content.innerHTML = data;
        var newHeight;
        var realHeight = content.parentElement.scrollHeight;
        var bodyHeight = window.innerHeight;
        if ((realHeight + 60)> bodyHeight) {
            newHeight = bodyHeight - 50;
            contentWrap.style.marginTop = "2em";
        } else {
            contentWrap.style.marginTop = "6em";
            newHeight = realHeight + 5; //just for sure
        }
        contentWrap.style.height = newHeight + "px";
        btn.style.display = "block";
        if (submitBtn) {
            submitBtn.e.innerHTML = submitBtn.html;
            submitBtn = null;
        }

        //sef FORM focus
        setTimeout(function() {
            var popupForm = popup.getElementsByTagName("form");
            if (popupForm.length !==0) {
                try {
                    popupForm[0].querySelector('[type=text]').focus();
                } catch(e) {/*no text field*/}
            }
        }, 100);

        setTimeout(function() {
            var imgs = content.querySelectorAll('img');
            if (imgs) {
                for (var i=0; i<imgs.length; i++) {
                    imgs[i].addEventListener('load', function() {
                        updatePopupSize();
                    }, false);
                }
            }
        }, 50);

        setTimeout(updatePopupSize, 20);

        if (chainedFunction) {
            console.log(chainedFunction);
            chainedFunction();
        }

    }

    btn.style.display = "none";
    //pure popup display of part of page
    try {
        if (a.href.substr(window.location.origin.length).indexOf("#") === 0) {
            return;
        }
    } catch(e) {}

    if (a.hasOwnProperty("offline")) {
        if (popup.style.display === "block") {
            //hide old popup
            btn.click();
        }

        //offline
        displayFunction('<div class="'+(a.class || "")+'" style="padding-top:'+(a.paddingTop || 0)+'">'+a.href+"</div>");
    } else {
        //online
        if (isImage) {
            //img
            var img = document.createElement("img");
            img.src = href;
            img.onload = function() {
                var w = img.width;
                var h = img.height;
                if (img.height > document.documentElement.clientHeight) {
                    h = document.documentElement.clientHeight - 20;
                    w = Math.round(h / (img.height / img.width))
                }
                if (w > document.documentElement.clientWidth) {
                    w = document.documentElement.clientWidth - 20;
                    h = Math.round(w / (img.width / img.height));
                }
                if (w > 640) {
                    contentWrap.style.maxWidth = w+"px";
                }
                contentWrap.style.width = w + "px";
                contentWrap.style.height = h+ "px";
                contentWrap.style.margin = "auto";
                content.appendChild(img);
                content.style.padding = 0;
                contentWrap.style.overflow = "hidden";
            }
        } else {
            //text, in other words, real link
            //NOTE popup param removed, ajax check instead
//            if (href.indexOf("?") > -1) {
//                href += "&popup";
//            } else {
//                href += "?popup";
//            }
//            if (chainedFunction) {
//                ajax.get(href, {}, function(data) {
//                   displayFunction(data);
//                    chainedFunction();
//                });
//            } else {
                ajax.get(href, {}, function(data) {
                    displayFunction(data);
                });
//            }

        }
    }
    var removal;
    //bind window event handler
    var popupRemoval = function(event) {
        //only when clicked on overlay
        if (event.target === popup || event.target.tagName.toLowerCase() === "img") removal(event);
    }

    removal = function(event) {
        //catch esc
        if (event.keyCode === 27 || event.type === 'click') {
            popup.style.display = "none";
            content.innerHTML = "";
            contentWrap.style.height = "";
            contentWrap.className = contentWrap.className.replace(' fade-text', '');
            contentWrap.style.width = "";
            window.removeEventListener('keyup', removal);
            btn.removeEventListener('click', removal);
            popup.removeEventListener('click', popupRemoval);
            if (isImage) {
                content.style.padding = "";
                contentWrap.style.overflow = "auto";
                contentWrap.style.maxWidth = "";
                contentWrap.style.height = contentWrap.style.width = "";
            }

        }
    }
    btn.addEventListener('click', removal, false);
    //handler overlay click
    popup.addEventListener('click', popupRemoval, false);
    window.addEventListener('keyup', removal, false);
    return popup;
}

function updatePopupSize() {
    var contentWrap = document.getElementById('center-container').firstElementChild;
    var realHeight = contentWrap.scrollHeight;
    var bodyHeight = window.innerHeight;
    if ((realHeight + 60)> bodyHeight) {
        newHeight = bodyHeight - 50;
        contentWrap.style.marginTop = "2em";
    } else {
        contentWrap.style.marginTop = "6em";
        newHeight = realHeight + 5; //just for sure
    }
    contentWrap.style.height = (newHeight + 10)+ "px";
}


function uploadForm(f, callback) {
    ajax.contentType = f.enctype;
    ajax.fileUpload = ajax.contentType === "multipart\/form-data";
    postForm(f, f.action, callback);
}


function postForm(f, event, callback) {
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
                    clicked.innerHTML = '&nbsp;<img src="/public/assets/icons/loading.gif" class="loader-indicator">&nbsp;';
                }

            }
            clearFormErrors(f.querySelectorAll(".error"));
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


function navigate(month, year) {
//    var wrap = document.querySelector("#calendar-wrap");
    var wrap = document.querySelectorAll(".calendar-wrap");
    if (wrap.length === 0) return;
//    wrap.style.backgroundImage = "url(/style/loading.gif)";
    for(var i = 0; i < wrap.length; i++) {
        wrap[i].style.backgroundImage = "url(/style/loading.gif)";
    }
    //calendar init
    ajax.get("/logic/calendar/calendar.php", {month: month || "a", year: year || "a"}, function(data) {
        for(var i = 0; i < wrap.length; i++) {
            wrap[i].style.backgroundImage = "";
            wrap[i].innerHTML = data;
        }
    });
}

function antis(el, formName) {
    document.forms[formName].elements.check.previousElementSibling.innerHTML = el.value.length;
    document.forms[formName].elements.check.setAttribute("placeholder", el.value.length +   parseInt(document.forms[formName].elements.seed.value));
}

function permanentHide(e) {
    e.target.parentNode.style.display = "none";
    var list = localStorage.getItem("hidden");
    var ids = [];
    if (list !== null) {    //load already saved items
        ids = JSON.parse(list);
    }
    ids.push(e.target.parentNode.id);
    localStorage.setItem("hidden", JSON.stringify(ids));
    document.querySelector("#reveal-button").style.visibility = "visible";
}

function toggleLang(a) {
    var engToggle = (a.className === "en") ? true : false;
    var engPage = window.location.pathname.indexOf("en/") > -1;
    //lang is EN on EN page
    if ((engToggle && engPage) || (!engToggle && !engPage)) {
        return false;
    }
    if (engToggle) {
        window.location = "/en" + window.location.pathname;
    } else if (engPage) {
        window.location = window.location.pathname.replace("en/","");
    }
}

function closeNotification(el) {
    var title = el.getAttribute("data-title");
    if (title) {
        var expire = new Date(0);
        expire.setSeconds(parseInt(el.getAttribute("data-expire")));
        var expString = expire.toUTCString();
        var path = el.getAttribute("data-scope");
        var domainSplit = location.host.split('.');
        var domain = '';
        if (domainSplit.length === 3) {     //full domain ie: www.kiwi.cz
            domain = '.' + domainSplit[1] + '.' + domainSplit[2];
        } else {
            domain = '.' + domainSplit[0] + '.' + domainSplit[1];
        }
        document.cookie = title+"=hidden; expires=" + expString + "; domain=" + domain + "; path="+path;
        //remove item from DOM
        while(el.className.indexOf('wrapper') === -1) {
            el = el.parentNode;
        }
    }
    if (title === null) {
        el = el.parentNode;
    }
    el.parentNode.removeChild(el);
}

window.onload = function() {
    //get switcher
    var wrap = document.querySelector(".switch-wrap");
    //apply switcher only to relevant pages, according to found .switch-wrap element
    if (wrap !== null) {
        var switchControl = (function() {
            var act = null;
            return {
                switch: function() {
                    //remove previous style
                    if (act!==null) {
                        act.className = act.className.replace(" active", "");
                    }
                    //add actual
                    var hash = window.location.hash;
                    if (hash !== "") {
                        var el = wrap.querySelector(("a[href='"+hash+"']"))
                        el.className += " active";
                        act = el;
                    }
                }
            };
        })();
        window.onhashchange = switchControl.switch;
        switchControl.switch();
    }
    /* set autosizing textareas */
    var textareas = document.querySelectorAll("textarea");
    if (textareas !== null) {
        for (var i=0, l = textareas.length; i < l; i++) {
            if (textareas[i].className.indexOf('no-resize') === -1) {
                autosize(textareas[i]);
            }
        }
    }
};
