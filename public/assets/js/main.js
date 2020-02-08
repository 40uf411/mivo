var base_url = "http://127.0.0.1:8000/";
var poserts_url = base_url + "assets/img/posters/";
var poserts_big_url = base_url + "assets/img/posters_big/";
var avatars_url = base_url + "assets/img/avatars/";
var formData = new FormData();

var movie_id, user_id;

var id;
var token = "";
var tuen = null;
var emotion = null;
var gender = null;
var context = null

var reco_url = null;
var audio_reco_results = [];

/********************************************************* startRecording, stopRecording
 */
var record_to_send = false;
var audio_blob;
var audio = document.querySelector('audio');

function captureMicrophone(callback) {
    btnReleaseMicrophone.disabled = false;
    if (microphone) {
        callback(microphone);
        return;
    }
    if (typeof navigator.mediaDevices === 'undefined' || !navigator.mediaDevices.getUserMedia) {
        alert('This browser does not supports WebRTC getUserMedia API.');
        if (!!navigator.getUserMedia) {
            alert('This browser seems supporting deprecated getUserMedia API.');
        }
    }
    navigator.mediaDevices.getUserMedia({
        audio: isEdge ? true : {
            echoCancellation: false
        }
    }).then(function (mic) {
        callback(mic);
    }).catch(function (error) {
        alert('Unable to capture your microphone. Please check console logs.');
        console.error(error);
    });
}

function replaceAudio(src) {
    var newAudio = document.createElement('audio');
    newAudio.controls = true;
    newAudio.autoplay = true;
    if (src) {
        newAudio.src = src;
    }

    var parentNode = audio.parentNode;
    parentNode.innerHTML = '';
    parentNode.appendChild(newAudio);
    audio = newAudio;
}

var isEdge = navigator.userAgent.indexOf('Edge') !== -1 && (!!navigator.msSaveOrOpenBlob || !!navigator.msSaveBlob);
var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
var recorder; // globally accessible
var microphone;
var btnStartRecording = document.getElementById('btn-start-recording');
var btnStopRecording = document.getElementById('btn-stop-recording');
var btnReleaseMicrophone = document.querySelector('#btn-release-microphone');
var btnDownloadRecording = document.getElementById('btn-download-recording');
var audio_blob = null;

function startRecording() {
    captureMicrophone(function (microphone) {
        recorder = RecordRTC(microphone, {
            type: 'audio',
            recorderType: StereoAudioRecorder,
            numberOfAudioChannels: 1,
            desiredSampRate: 16000
        });
        recorder.startRecording();
        // release microphone on stopRecording
        recorder.microphone = microphone;
    });
    record_to_send = true;
};
btnStartRecording.onclick = function () {
    startRecording();
};

function stopRecording() {
    console.log("help");

    recorder.stopRecording();
};
btnStopRecording.onclick = function () {
    stopRecording();
    releaseMicrophone();
    setTimeout(function () {
        console.log(recorder.getBlob());
    }, 300)

    //downloadRecording();
};

function releaseMicrophone() {
    console.log("hello");

    if (microphone) {
        microphone.stop();
        microphone = null;
    }
    if (recorder) {
        // click(btnStopRecording);
    }
};
btnReleaseMicrophone.onclick = function () {
    releaseMicrophone();
};

function downloadRecording() {
    console.log("hi");
    setTimeout(function () {
        console.log(recorder.getBlob());
        if (!recorder || !recorder.getBlob()) return;
        if (isSafari) {
            recorder.getDataURL(function (dataURL) {
                SaveToDisk(dataURL, getFileName('wav'));
            });
            return;
        }
        var blob = recorder.getBlob();
        audio_blob = blob;
        var file = new File([blob], getFileName('wav'), {
            type: 'audio/wav'
        });
        invokeSaveAsDialog(file);
    }, 300)


};
btnDownloadRecording.onclick = function () {
    downloadRecording();
};

function click(el) {
    el.disabled = false; // make sure that element is not disabled
    var evt = document.createEvent('Event');
    evt.initEvent('click', true, true);
    el.dispatchEvent(evt);
}

function getRandomString() {
    if (window.crypto && window.crypto.getRandomValues && navigator.userAgent.indexOf('Safari') === -1) {
        var a = window.crypto.getRandomValues(new Uint32Array(3)),
            token = '';
        for (var i = 0, l = a.length; i < l; i++) {
            token += a[i].toString(36);
        }
        return token;
    } else {
        return (Math.random() * new Date().getTime()).toString(36).replace(/\./g, '');
    }
}

function getFileName(fileExtension) {
    var d = new Date();
    var year = d.getFullYear();
    var month = d.getMonth();
    var date = d.getDate();
    return 'RecordRTC-' + year + month + date + '-' + getRandomString() + '.' + fileExtension;
}

function SaveToDisk(fileURL, fileName) {
    // for non-IE
    if (!window.ActiveXObject) {
        var save = document.createElement('a');
        save.href = fileURL;
        save.download = fileName || 'unknown';
        save.style = 'display:none;opacity:0;color:transparent;';
        (document.body || document.documentElement).appendChild(save);
        if (typeof save.click === 'function') {
            save.click();
        } else {
            save.target = '_blank';
            var event = document.createEvent('Event');
            event.initEvent('click', true, true);
            save.dispatchEvent(event);
        }
        (window.URL || window.webkitURL).revokeObjectURL(save.href);
    }
    // for IE
    else if (!!window.ActiveXObject && document.execCommand) {
        var _window = window.open(fileURL, '_blank');
        _window.document.close();
        _window.document.execCommand('SaveAs', true, fileName || fileURL)
        _window.close();
    }
}
/******************************************************************* */

var movies_list = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
        url: base_url + "search_results/?movie=%QUERY",
        wildcard: '%QUERY'
    }
});

$('#global-search').typeahead(null, {
    hint: true,
    highlight: true,
    minLength: 2,
    name: 'movies-list',
    display: 'title',
    limit: 10,
    source: movies_list,
    templates: {
        empty: [
            '<div class="empty-message is-movie">',
            'unable to find any movie that match the current query',
            '</div>'
        ].join('\n'),
        suggestion: Handlebars.compile('<div>{{title}}</div>')
    }
});

function send_emo_reco_request(blob) {
    if (turn == 0) {
        return send_emo_reco_request_for_user(blob);
    }
    console.log("nooooooooooooooooooo");
    
    formData = new FormData();
    formData.append('audio', blob);
    
    formData.append('movie_id', movie_id);

    if (token != "")
        formData.append('token', token);

    // Use `jQuery.ajax` method

    return get_data({
        "url": base_url + reco_url,
        "formData": formData,
        "success": function () {
            console.log("i got the intro");
        },
        "fail": function () {}
    });
}
function send_emo_reco_request_for_user(blob) { 
    if (turn == 1) {
        return send_emo_reco_request(blob);
    }  
    console.log("heeeeeeeeeeeeeeeeeeelp");
     
    formData = new FormData();
    formData.append('audio', blob);
    
    formData.append('user_id', user_id);

    formData.append('token', token);

    for (var pair of formData.entries()) {
        console.log(pair[0]+ ', ' + pair[1]); 
    }

    // Use `jQuery.ajax` method

    return get_data({
        "url": base_url + reco_url,
        "formData": formData,
        "success": function () {
            console.log("i got the intro");
        },
        "fail": function () {}
    });
}
function get_data(data) {
    var a = $.ajax(data["url"], {

        async: false,

        method: "POST",

        data: data["formData"],

        processData: false,

        contentType: false,

        xhrFields: {
            // 'Access-Control-Allow-Credentials: true'.
            withCredentials: false
        },
        headers: {
            "Access-Control-Allow-Origin": true
        },
        success: function (response) {
            data["success"]();
        },
        error: function (response) {
            data["fail"]();
        }
    });

    return JSON.parse(a.responseText);
}

function follow_request() {
    formData = new FormData();
    formData.append("token", token);
    formData.append("user_id", user_id);

    var d = get_data({
        "url": base_url + "follow",
        "formData": formData,
        "success": function () {},
        "fail": function () {}
    });
    if (d["status"] == "error") {
        notification({
            "bc": "red-text",
            "big": "Error!",
            "message": d["msg"]
        });
    } else {
        if (d["action"] == "following") {
            notification({
                "bc": "text-green",
                "big": "Done!",
                "message": "you started following " + d["data"]["full_name"]
            });
        } else {
            notification({
                "bc": "text-green",
                "big": "Done!",
                "message": "you stopped following " + d["data"]["full_name"]
            });
        }
    }
}

function fav_request() {

    formData = new FormData();
    formData.append("token", token);
    formData.append("movie_id", movie_id);

    var d = get_data({
        "url": base_url + "favorite",
        "formData": formData,
        "success": function () {},
        "fail": function () {}
    });
    if (d["status"] == "error") {
        notification({
            "bc": "red-text",
            "big": "Error!",
            "message": d["msg"]
        });
    } else {
        if (d["action"] == "fav") {
            notification({
                "bc": "text-green",
                "big": "Done!",
                "message": "you added " + d["data"]["title"] + " to your favorites list."
            });
        } else {
            notification({
                "bc": "text-green",
                "big": "Done!",
                "message": "you removed " + d["data"]["title"] + " to your favorites list."
            });
        }
    }
}

function wl_request() {

    formData = new FormData();
    formData.append("token", token);
    formData.append("movie_id", movie_id);

    var d = get_data({
        "url": base_url + "wish_list",
        "formData": formData,
        "success": function () {},
        "fail": function () {}
    });
    if (d["status"] == "error") {
        notification({
            "bc": "red-text",
            "big": "Error!",
            "message": d["msg"]
        });
    } else {
        if (d["action"] == "added") {
            notification({
                "bc": "text-green",
                "big": "Done!",
                "message": "you added " + d["data"]["title"] + " to your wish-list."
            });
        } else {
            notification({
                "bc": "text-green",
                "big": "Done!",
                "message": "you removed " + d["data"]["title"] + " to your wish-list."
            });
        }
    }
}

function block_request() {

    formData = new FormData();
    formData.append("token", token);
    formData.append("user_id", user_id);

    var d = get_data({
        "url": base_url + "block",
        "formData": formData,
        "success": function () {},
        "fail": function () {}
    });
    if (d["status"] == "error") {
        notification({
            "bc": "red-text",
            "big": "Error!",
            "message": d["msg"]
        });
    } else {
        if (d["action"] == "added") {
            notification({
                "bc": "text-green",
                "big": "Done!",
                "message": "you added " + d["data"]["full_name"] + " to your wish-list."
            });
        } else {
            notification({
                "bc": "text-green",
                "big": "Done!",
                "message": "you removed " + d["data"]["full_name"] + " to your wish-list."
            });
        }
    }
}

function block_movie_request() {

    formData = new FormData();
    formData.append("token", token);
    formData.append("movie_id", movie_id);

    var d = get_data({
        "url": base_url + "block_movie",
        "formData": formData,
        "success": function () {},
        "fail": function () {}
    });
    if (d["status"] == "error") {
        notification({
            "bc": "red-text",
            "big": "Error!",
            "message": d["msg"]
        });
    } else {
        if (d["action"] == "block_movie") {
            notification({
                "bc": "text-green",
                "big": "Done!",
                "message": "you ll no longer see the movie: " + d["data"]["title"] + " ."
            });
        } else {
            notification({
                "bc": "text-green",
                "big": "Done!",
                "message": "you removed the movie " + d["data"]["title"] + " from your blocked-list."
            });
        }
    }
}

function make_user_card(data) {
    return `
    <div class="card is-user" style="width: 18rem; max-width: 17%; min-width: 12.5%;" user_id=` + data["id"] + `>
        <img class="card-img-top poster" src="` + avatars_url + data["profile_pic"] + `"
            alt="Card image cap">
        <div class="card-footer">
            <small class="text-muted">` + data["full_name"] + `</small>
        </div>
    </div>`;
}

function make_users_section(data) {

    var users = "",
        element = [];
    for (let i = 0; i < data["users"].length; i++) {
        element = data["users"][i];
        users += make_user_card(element);
    }
    users = (users == "") ? "<p style='    text-align: center !important;width:100%'>No movie found.</p>" : users;
    return `
    <div class="a_section part-` + data["number"] + ` mb-4 ` + data["classes"] + ` " style="` + data["style"] + `">
        <h2>` + data["title"] + `</h2>
        <hr>
        <div class="list-of-new-users">
            <div class="card-deck">
            ` + users + `
            </div>
        </div>
    </div>
    `;
}

function make_movie_card(data, big = false) {
    return `
    <div class="card is-movie with_effect" movie_id=` + data["id"] + `>
        <img class="card-img-top poster" src="` + ((big) ? poserts_big_url + data["poster_big"] : poserts_url + data["poster"]) + `"
            alt="Card image cap">
        <div class="card-body">
            <h2 class="card-title">` + data["title"] + `</h2>
            <p class="card-text">` + data["description"] + `</p>
        </div>
        <div class="card-footer">
            <small class="text-muted">` + data["year"] + `</small>
        </div>
    </div>`;
}

function make_special_movie_card(data) {
    return `
    <div class="card is-movie" style="width: 18rem;" movie_id=` + data["id"] + `>
        <img class="card-img-top" src="` + poserts_url + data["poster"] + `" alt="Card image cap">
        <div class="card-footer">
            ` + data["title"] + ` 
        </div>
    </div>`;
}

function make_movies_section(data) {

    var movies = "",
        element = [],
        big,
        num = (data["movies"].length % 5);
    for (let i = 0; i < data["movies"].length; i++) {
        big = ((data["movies"].length - num) <= i)
        element = data["movies"][i];
        movies += make_movie_card(element, big);
    }
    movies = (movies == "") ? "<p style='    text-align: center !important;width:100%'>No movie found.</p>" : movies;
    return `
    <div class="a_section part-` + data["number"] + ` mb-4 ` + data["classes"] + ` " style="` + data["style"] + `">
        <h2>` + data["title"] + `</h2>
        <hr>
        <div class="list-of-new-movies">
            <div class="card-deck">
            ` + movies + `
            </div>
        </div>
    </div>
    `;
}

function make_special_movies_section(data) {

    var movies = "",
        element = [];
    for (let i = 0; i < data["movies"].length; i++) {
        element = data["movies"][i];
        movies += make_special_movie_card(element);
    }
    movies = (movies == "") ? "<p style='    text-align: center !important;width:100%'>No movie found.</p>" : movies;
    return `
    <div class="a_section part-` + data["number"] + ` mb-4 p-4 special ` + data["classes"] + `" style="` + data["style"] + `">
        <h2>` + data["title"] + `</h2>
        <hr>
        <div class="list-of-new-movies">
            <div class="card-deck">
            ` + movies + `
            </div>
        </div>
    </div>`;
}

function make_profile_intro(data) {    
    var str = (data['in']) ?
        `<hr>
    <p>
        <b><i class="icofont-list"></i> Last log-in:</b> ` + data["last_login"] + `
    </p>
    <p>
        <b><i class="icofont-list"></i> THis session creation:</b> ` + data["session_creation"] + `
    </p>` : ``;
    return `
<div class="row mb-4" style="margin: 0 15px;">
    <div class="card profile-area w-100" >
        <div class="background-area" >
            <img src="` + base_url + "assets/img/covers/" + data["profile_cover"] + `" alt="">
        </div>
        <img src="` + base_url + "assets/img/avatars/" + data["profile_pic"] + `" alt=""
            class="profile-pic">
        <div class="info-area">
            <div class="fnf"><p>following : ` + data["follower"] + `</p> | <p>followers : ` + data["followed"] + `</p> </div>
            <div class="name" <p><b>` + data["full_name"] + `</b></p></div>
        </div>
    </div>
</div>
<div class="row profile justify-content-between mb-4" style="margin: 0 15px;">
    <div class="card col align-self-start basic-info ml-3">
        <h2>Basic info</h2><hr>
        <p>
            <b><i class="icofont-ui-email"></i> Email:</b> ` + data["email"] + `</br>
        </p>
        <p>
            <b><i class="icofont-ui-user"></i> Age:</b> ` + data["age"] + `</br>
        </p>
        <p>
            ` + ((data["gender"] == 0) ? '<b><i class="icofont-user-male"></i> Gender:</b> Male' : '<b><i class="icofont-user-female"></i> Gender:</b> Female') + `</br>
        </p>
        <p>
            <b><i class="icofont-search-user"></i> Intrested in:</b> ` + ((data["intrested_in"] == 0) ? "Males" : "Females") + `
        </p>` + str + `
    </div>
    <div class="card col-9 stats-info">
        <h2>Stats</h2><hr>
        <div class="row">
            <div class="col-6"><canvas id="p-page-canvas-1"></canvas></div>
            <div class="col-6"><canvas id="p-page-canvas-2"></canvas></div>

        </div>
    </div>
</div>`;
}

function make_intro(data) {

    return `
    <div class="card text-white intro-card mb-4 is-movie" movie_id=` + data["id"] + `>
        <img class="card-img" src="` + poserts_big_url + data["poster_big"] + `" alt="Card image">
        <div class="card-img-overlay" style="" id="intro-card-img-overlay">
            <div class="context">
                <h1 class="card-title">` + data["title"] + `</h1>
                <p class="card-text">` + data["description"] + `</p>
                <p class="card-year">` + data["year"] + `</p>
                <p class="card-year">` + data["rate"] + `/10</p>
                <p class="card-year">` + data["rank"] + `</p>
            </div>
        </div>
    </div>`;
}

function load_home() {

    var html, new_movies, best_movies, most_liked_movies;

    var intro = get_data({
        "url": base_url + "intro",
        "formData": formData,
        "success": function () {
            console.log("i got the intro");
        },
        "fail": function () {}
    });
    html = make_intro(intro);

    new_movies = get_data({
        "url": base_url + "new",
        "formData": formData,
        "success": function () {},
        "fail": function () {}
    });
    html += make_movies_section({
        "number": 1,
        "title": 'New movies <i class="icofont-newspaper"></i>',
        "movies": new_movies
    });

    best_movies = get_data({
        "url": base_url + "best",
        "formData": formData,
        "success": function () {},
        "fail": function () {}
    });
    html += make_movies_section({
        "number": 2,
        "title": 'best movies <i class="icofont-chart-growth"></i>',
        "movies": best_movies
    });

    most_liked_movies = get_data({
        "url": base_url + "most_liked",
        "formData": formData,
        "success": function () {},
        "fail": function () {}
    });
    html += make_movies_section({
        "number": 3,
        "title": 'Most liked movies <i class="icofont-like"></i>',
        "movies": most_liked_movies
    });

    $("#content-side").html(html);


    post_generation();
}

function load_feed() {
    formData = new FormData();
    formData.append("token", token);


    var bgs = [
        "jeshoots-com-606648-unsplash.jpg",
        "vince-gaspar-503209-unsplash.jpg"
    ]
    var wallpaper = bgs[Math.floor(Math.random() * bgs.length)];
    var html = "<div style='background: url(" + base_url + "assets/img/bg/myke-simon-1037761-unsplash.png); background-size:100%;    background-repeat: no-repeat;'><div class='feed-intro'><h1>HERE IS YOUR PERSONALIZED FEED!</H1><H4>Enjoy!</H4></div>";
    /*
        if (emotion != null) {
            formData.append("emotion", token);
            var f_likes = get_data({
                "url": base_url + "similar_to_emo",
                "formData": formData,
                "success": function () {},
                "fail": function () {}
            });
            html += make_movies_section({
                "number": 0,
                "title": 'Based on your current emotion  <i class="icofont-users-social"></i>',
                "movies": f_likes["data"]
            });
        }
        if (gender != null) {
            formData.append("gender", gender);
            var f_likes = get_data({
                "url": base_url + "similar_to_gender",
                "formData": formData,
                "success": function () {},
                "fail": function () {}
            });
            html += make_movies_section({
                "number": 0,
                "title": 'People with similar gender tend to like this  <i class="icofont-users-social"></i>',
                "movies": f_likes["data"]
            });
        }*/

    var f_likes = get_data({
        "url": base_url + "followers_likes",
        "formData": formData,
        "success": function () {},
        "fail": function () {}
    });
    html += make_movies_section({
        "number": 1,
        "title": 'People you follow likes  <i class="icofont-like"></i>',
        "movies": f_likes["data"]
    });

    var similar = get_data({
        "url": base_url + "similar_to_taste",
        "formData": formData,
        "success": function () {},
        "fail": function () {}
    });
    html += make_special_movies_section({
        "number": 1,
        "title": 'You might like this  <i class="icofont-favourite"></i>',
        "movies": similar["data"],
        "classes": "card with-margin",
        "style": "background: url(" + base_url + "assets/img/bg/Untitled.png); margin: 60px 15px!important"
    });

    var s_users = get_data({
        "url": base_url + "similar_users",
        "formData": formData,
        "success": function () {},
        "fail": function () {}
    });
    html += make_users_section({
        "number": 1,
        "title": 'People with similar tastes  <i class="icofont-users-social"></i>',
        "users": s_users["data"]
    });

    var f_likes = get_data({
        "url": base_url + "similar_to_fav",
        "formData": formData,
        "success": function () {},
        "fail": function () {}
    });
    html += make_movies_section({
        "number": 1,
        "title": 'MOvies similar to what you like  <i class="icofont-like"></i>',
        "movies": f_likes["data"],
        "classes": "without-margin special",
        "style": "background: url(" + base_url + "assets/img/bg/" + wallpaper + "); padding: 75px 25px"
    });
    html += "</div>"


    $("#content-side").html(html);
    post_generation();
}

function load_p_page(client_id, sent_data = false) {
    if (!sent_data) {
        formData = new FormData();
        var me = false;
        if (client_id == id) {
            me = true;
            formData.append("token", token);
        } else
            formData.append("id", client_id);
    
        var data = get_data({
            "url": base_url + "user",
            "formData": formData,
            "success": function () {},
            "fail": function () {}
        });
    }
    else{
        data = client_id;
    }


    var html;

    if (data["status"] == "ok") {

        html = '<div class="profile-page">';

        html += make_profile_intro(data["data"]["person"]);
        if (me) {
            html += make_movies_section({
                "number": 2,
                "title": 'Favorites list <i class="icofont-favourite"></i>',
                "movies": data["data"]["fav"]
            });
            html += make_movies_section({
                "number": 3,
                "title": 'Wish list <i class="icofont-list"></i>',
                "movies": data["data"]["wl"]
            });

            html += make_users_section({
                "number": 4,
                "title": 'People you are following <i class="icofont-users-social"></i>',
                "users": data["data"]["followers"]
            });

            html += make_users_section({
                "number": 4,
                "title": 'People you are blocking <i class="icofont-list"></i>',
                "users": data["data"]["blocked_users"]
            });

            html += make_movies_section({
                "number": 3,
                "title": 'Movies you are blocking <i class="icofont-list"></i>',
                "movies": data["data"]["blocked_movies"]
            });
        }

        html += "</div>";
    } else {
        html = '<p class="text-danger">Error:' + data["msg"] + '</p>'
    }

    $("#content-side").html(html);
    var ctx = $("#p-page-canvas-1")[0].getContext('2d');

    make_chart(ctx, "radar", {
        "title": "Movie componants",
        "labels": ["action", "animated", "adventure", "bio", "crime", "comedy", "drama", "fantasy", "historical", "horror", "mystery", "political", "romance", " Sci_Fi", "war"],
        "values": [
            data["data"]["person"]["action"],
            data["data"]["person"]["animated"],
            data["data"]["person"]["adventure"],
            data["data"]["person"]["bio"],
            data["data"]["person"]["crime"],
            data["data"]["person"]["comedy"],
            data["data"]["person"]["drama"],
            data["data"]["person"]["fantasy"],
            data["data"]["person"]["historical"],
            data["data"]["person"]["horror"],
            data["data"]["person"]["mystery"],
            data["data"]["person"]["political"],
            data["data"]["person"]["romance"],
            data["data"]["person"]["science_fiction"],
            data["data"]["person"]["war"],
        ]
    });
    var ctx1 = $("#p-page-canvas-2")[0].getContext('2d');
    make_chart(ctx1, "line", {
        "title": "On mood",
        "labels": ["Likes on emo 0", "Dislikes on emo 0", "Likes on emo 1", "Dislikes on emo 1", "Likes on emo 2", "Dislikes on emo 2"],
        "values": data["data"]["mood"]

    });
    post_generation();
}

function load_similar(movie_id) {
    formData = new FormData();
    formData.append("movie_id", movie_id);
    var html = "",
        data = get_data({
            "url": base_url + "similar_to_movie",
            "formData": formData,
            "success": function () {},
            "fail": function () {}
        });
    html += make_movies_section({
        "number": 3,
        "title": 'Movies similar to "' + data["title"] + '" <i class="icofont-favourite"></i>',
        "movies": data["data"]
    });

    $("#content-side").html(html);


    post_generation();
}

function post_generation() {
    $(".is-user").npContextMenu({
        menuSelector: "#user-rightclick-menu"
    });
    $(".is-user").contextmenu(function () {
        user_id = $(this).attr("user_id");
    });
    $(".is-user").click(function () {
        turn = 0;
        user_id = $(this).attr("user_id");
        emo_recorder_on_user(this);
    });
    $(".is-movie").npContextMenu({
        menuSelector: "#movie-rightclick-menu"
    });
    $(".is-movie").contextmenu(function () {
        movie_id = $(this).attr("movie_id");
    });
    $(".is-movie").click(function () {
        turn = 1;
        movie_id = $(this).attr("movie_id");
        emo_recorder_on_movie(this);
    });
    $(".a_section .is-movie.with_effect").mouseenter(function () {

        if ($('#hover-effect-sound').length)
            $('#hover-effect-sound').remove();

        $("<audio></audio>").attr({
            'src': base_url + 'assets/audios/sound1.mp3',
            'volume': 0.4,
            'autoplay': 'autoplay'
        }).attr("id", "hover-effect-sound").appendTo("body");
    });
    window.scrollTo(0, 0);
}

function login() {

    formData.append('email', $("#email").val());
    formData.append('pass', $("#pass").val());
    $.ajax(base_url + 'login/', {
        method: "POST",

        data: formData,

        processData: false,

        contentType: false,

        xhrFields: {
            // 'Access-Control-Allow-Credentials: true'.
            withCredentials: false
        },

        headers: {
            "Access-Control-Allow-Origin": true
        },

        success: function (response) {
            response = JSON.parse(response);

            if (response["status"] == "error") {
                $("#login-error-box").html(response["msg"]);
            } else {
                token = response["token"];
                id = response["id"];
                login_page_effects(response["data"]);
            }



            console.log("login request sent.");
        },
        error: function (response) {
            response = JSON.parse(response);
            console.log("error! while connecting to server.");
            console.log(response);
        }
    });
}

function login_page_effects(data) {

    var words = [
        "Heyyy!",
        "Hola!",
        "Long time no see!",
        "Whooha!"
    ]
    var word = words[Math.floor(Math.random() * words.length)];
    notification({
        "bc": "text-green",
        "big": word,
        "message": "welcome back " + data["name"]
    });
    $("#movie-rightclick-menu").html(`
    <li class="details-button" tabindex="-1">Show more details</li>
    <li class="fav-button" tabindex="-1">Add to Favorites</li>
    <li class="wl-button" tabindex="-1">Add to wish-list</li>
    <li class="block-button" tabindex="-1">block</li>
    <li class="divider"></li>
    <li class="similar-button" tabindex="-1">Find similar movies</li>`);
    $("#movie-rightclick-menu .details-button").click(function () {
        load_movie_details(movie_id);
    });
    $("#movie-rightclick-menu .fav-button").click(function () {
        fav_request();
    });
    $("#movie-rightclick-menu .wl-button").click(function () {
        wl_request();
    });
    $("#movie-rightclick-menu .block-button").click(function () {
        block_movie_request();
    });
    $("#user-rightclick-menu .block-button").click(function () {
        block_request();
    });

    $("#movie-rightclick-menu .similar-button").click(function () {
        load_similar(movie_id);
    });
    $("#nav-items").html(`
    <li class="nav-item dropdown p-page">
        <a class="nav-link dropdown-toggle" href="#" id="profile" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">` + data["name"] + `</a>
            <div class="dropdown-menu" aria-labelledby="profile" id="loged-in-dropdown">
                <a class="dropdown-item" href="#" id="profile-button">Profile <i class="icofont-ui-user"></i></a>
                <a class="dropdown-item" href="#" id="feed-button">My feed <i class="icofont-rss-feed"></i></a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" id="logout-button">log-out <i class="icofont-logout"></i></a>
          </div>
    </li>
    <li class="nav-item home active">
        <a class="nav-link" href="#">Home</a>
    </li>`);
    $(".profile-card").css("filter", "none");
    $(".profile-card .card-title").html(data["name"]);
    $(".main-container .main-row .profile-card .avatar img").attr("src", "/assets/img/avatars/" + data["profile_pic"])
    $(".profile-card .card-text").html(`
    ` + `<br>` + data["action"] + ` : action
    ` + `<br>` + data["animated"] + ` : animated
    ` + `<br>` + data["adventure"] + ` : adventure
    ` + `<br>` + data["bio"] + ` : bio
    ` + `<br>` + data["crime"] + ` : crime
    ` + `<br>` + data["comedy"] + ` : comedy
    ` + `<br>` + data["drama"] + ` : drama
    ` + `<br>` + data["fantasy"] + ` : fantasy
    ` + `<br>` + data["historical"] + ` : historical
    ` + `<br>` + data["horror"] + ` : horror
    ` + `<br>` + data["mystery"] + ` : mystery
    ` + `<br>` + data["political"] + ` : political
    ` + `<br>` + data["romance"] + ` : romance
    ` + `<br>` + data["science_fiction"] + ` : science_fiction
    ` + `<br>` + data["war"] + ` : war`);

    $(".nav-item.home").click(function () {
        load_home();
    });

    $("#profile-button").click(function () {
        load_p_page(id);
    });
    $("#feed-button").click(function () {
        load_feed();
    });
    $("#logout-button").click(function () {
        location.reload();
    });
}

function emo_recorder_on_movie(movie) {
    if (!$(".mic-running-countdown").hasClass("active")) {
        reco_url = "upload_audio";
        startRecording();
        $(".mic-running-countdown").addClass("active");
        $(".mic-running-countdown").html("");
        $(".mic-running-countdown").startTimer({
            onComplete: function (element) {
                element.addClass('is-complete');
                element.removeClass("active");
                stopRecording();
                releaseMicrophone();
                setTimeout(function () {
                    if (record_to_send) {
                        var data = audio_reco_results = send_emo_reco_request(recorder.getBlob());
                        if (data == null) {
                            notification({
                                "bc": "red-text",
                                "big": "Error!",
                                "message": "no result from the recognition server."
                            });
                        } else {
                            if (data["status"] == "error") {
                                notification({
                                    "bc": "red-text",
                                    "big": "Error!",
                                    "message": data["msg"]
                                });
                            } else {
                                if (data["action"] == "following") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you started following " + data["data"]["full_name"]
                                    });
                                } else if (data["action"] == "unfollowing") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you stopped following " + data["data"]["full_name"]
                                    });
                                } else if (data["action"] == "block") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you blocked " + data["data"]["full_name"]
                                    });
                                } else if (data["action"] == "unblock") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you unblocked " + data["data"]["full_name"]
                                    });
                                } else if (data["action"] == "fav") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you added '" + data["data"]["title"] + "' to your favorites list"
                                    });
                                } else if (data["action"] == "unfav") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you removed '" + data["data"]["full_name"] + "' from your favorites list"
                                    });
                                } else if (data["action"] == "block_movie") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you blocked the movie '" + data["data"]["title"] + "'"
                                    });
                                } else if (data["action"] == "unblock_movie") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you unblocked the movie '" + data["data"]["title"] + "'"
                                    });
                                } else if (data["recommendation"] == true) {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Cool!",
                                        "message": "we got you some similar movies to '" + data["title"] + "' "
                                    });
                                }
                                emotion = data["reco"]["emotion"];
                                gender = data["reco"]["gender"];
                                context = data["reco"]["context"];

                            }
                        }
                        console.log(data);
                        do_action(data);
                        record_to_send = false;
                    }
                }, 1000);
            },
        });
    }

}

function emo_recorder_on_user(user) {
    if (!$(".mic-running-countdown").hasClass("active")) {
        reco_url = "upload_audio";
        startRecording();
        $(".mic-running-countdown").addClass("active");
        $(".mic-running-countdown").html("");
        $(".mic-running-countdown").startTimer({
            onComplete: function (element) {
                element.addClass('is-complete');
                element.removeClass("active");
                stopRecording();
                releaseMicrophone();
                setTimeout(function () {
                    if (record_to_send) {
                        console.log("help meeeeeeeeeeeeeeeee!");
                        
                        var data = audio_reco_results = send_emo_reco_request_for_user(recorder.getBlob());
                        if (data == null) {
                            notification({
                                "bc": "red-text",
                                "big": "Error!",
                                "message": "no result from the recognition server."
                            });
                        } else {
                            if (data["status"] == "error") {
                                notification({
                                    "bc": "red-text",
                                    "big": "Error!",
                                    "message": data["msg"]
                                });
                            } else {
                                if (data["action"] == "following") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you started following " + data["data"]["full_name"]
                                    });
                                } else if (data["action"] == "unfollowing") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you stopped following " + data["data"]["full_name"]
                                    });
                                } else if (data["action"] == "block") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you blocked " + data["data"]["full_name"]
                                    });
                                } else if (data["action"] == "unblock") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you unblocked " + data["data"]["full_name"]
                                    });
                                } else if (data["action"] == "fav") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you added '" + data["data"]["title"] + "' to your favorites list"
                                    });
                                } else if (data["action"] == "unfav") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you removed '" + data["data"]["full_name"] + "' from your favorites list"
                                    });
                                } else if (data["action"] == "block_movie") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you blocked the movie '" + data["data"]["title"] + "'"
                                    });
                                } else if (data["action"] == "unblock_movie") {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Done!",
                                        "message": "you unblocked the movie '" + data["data"]["title"] + "'"
                                    });
                                } else if (data["recommendation"] == true) {
                                    notification({
                                        "bc": "text-green",
                                        "big": "Cool!",
                                        "message": "we got you some similar movies to '" + data["title"] + "' "
                                    });
                                }
                                emotion = data["reco"]["emotion"];
                                gender = data["reco"]["gender"];
                                context = data["reco"]["context"];

                            }
                        }
                        console.log(data);
                        do_action(data);
                        record_to_send = false;
                    }
                }, 1000);
            },
        });
    }

}
function do_action(data) {
    if(data != null) {
        if ( data["recommendation"]) {
            var html = make_movies_section({
                "number": 3,
                "title": 'Movies similar "' + data['title'] + '"<i class="icofont-like"></i>',
                "movies": data["data"]
            });
            $("#content-side").html(html);
        }
        else if ( data["profile"] ) {
            var html = load_p_page(data, true);
            $("#content-side").html(html);
        }
        post_generation();
    }
}

function emo_recorder(movie) {
    if (!$(".mic-running-countdown").hasClass("active")) {
        reco_url = "emo_reco";
        startRecording();
        $(".mic-running-countdown").addClass("active");
        $(".mic-running-countdown").html("");
        $(".mic-running-countdown").startTimer({
            onComplete: function (element) {
                element.addClass('is-complete');
                element.removeClass("active");
                stopRecording();
                releaseMicrophone();
                setTimeout(function () {
                    if (record_to_send) {
                        var data = audio_reco_results = send_emo_reco_request(recorder.getBlob());
                        console.log(data);
                        if (data == null) {
                            notification({
                                "bc": "red-text",
                                "big": "Error!",
                                "message": "no result from the recognition server."
                            });
                        } else {
                            if (data["status"] == "error") {
                                notification({
                                    "bc": "red-text",
                                    "big": "Error!",
                                    "message": data["msg"]
                                });
                            } else {
                                notification({
                                    "big": "Done!",
                                    "message": "the system thinks you said: '" + data["text"] + "' ,Emotion: " + data["data"]["emotion"]
                                                + ",Gender: " + data["data"]["gender"] + " ,Context: " + data["data"]["context"]
                                });
                                emotion = data["data"]["emotion"];
                                gender = data["data"]["gender"];
                                context = data["data"]["context"];
                            }
                        }
                        record_to_send = false;
                    }
                }, 1000);
            },
        });
    }

}

function load_movie_details(m_id) {
    formData.append("movie_id", movie_id);
    var data = get_data({
        "url": base_url + "movie",
        "formData": formData,
        "success": function () {
            console.log("i got the movie " + movie_id + " details");
        },
        "fail": function () {}
    });
    var data = data["data"];

    var str = `
    <div class="row mb-4 w-100">
    <div class="card text-white intro-card mb-4 w-100 no-shadow">
        <img class="card-img"
            src="` + poserts_big_url + data["poster_big"] + `"
            alt="Card image">
        <div class="card-img-overlay" style="" id="intro-card-img-overlay">
            <div class="context">
                <img src="` + poserts_url + data["poster"] + `" class="" alt="">
                <h1 class="card-title">` + data["title"] + `</h1>
            </div>
        </div>
    </div>
    <div class="card w-100 transparent-bg no-shadow">
        <div class="card-body">
            <p class="card-text">` + data["description"] + `</p>
        </div>
    </div>
</div>
<div class="row justify-content-between mb-4 w-100 info">
    <div class="card col align-self-start basic-info transparent-bg ml-3 no-shadow">
        <h2>Basic info</h2>
        <hr>
        <p>
            <b> Year:</b> ` + data["year"] + `<br>
        </p>
        <p>
            <b> Rate:</b> ` + data["rate"] + `<br>
        </p>
        <p>
            <b> Rank:</b> ` + data["rank"] + `<br>
        </p>
        <p>
            <b> Language:</b> ` + data["lang"] + `
        </p>
        <hr>
        <p>
            <b> On fav-lists:</b> ` + data["fl"] + `<br>
        </p>
        <p>
            <b> On wish-list:</b> ` + data["wl"] + `<br><br>
        </p>
    </div>
    <div class="card col-9 stats-info transparent-bg no-shadow">
        <h2>Stats</h2>
        <hr>
        <div class="row">
            <div class="col-6" id="movie-shart-part-1">
            <canvas></canvas>
            </div>
            <div class="col-6" id="movie-shart-part-2">
            <canvas></canvas>
            </div>
        </div>
    </div>
</div>`;

    $("#movie-details-model .modal-dialog").html(str);
    var ctx = $("#movie-shart-part-1 canvas")[0].getContext('2d');

    make_chart(ctx, "bar", {
        "title": "Movie componants",
        "labels": ["action", "animated", "adventure", "bio", "crime", "comedy", "drama", "fantasy", "historical", "horror", "mystery", "political", "romance", " Sci_Fi", "war"],
        "values": data["cmp"]
    }, true);

    ctx = $("#movie-shart-part-2 canvas")[0].getContext('2d');
    make_chart(ctx, "line", {
        "title": "Movie expressions",
        "labels": ["Likes on emo 0", "Dislikes on emo 0", "Likes on emo 1", "Dislikes on emo 1", "Likes on emo 2", "Dislikes on emo 2"],
        "values": data["exp"]
    }, true);

    show_movie_details_modal();
    setTimeout(function () {
        $("#movie-details-model .modal-dialog .intro-card .card-img").css("animation", "poster_effect 15s ease")
    }, 1500)

}

function show_movie_details_modal() {
    $("#the-whole").addClass("active");
    $('#movie-details-model').modal({
        backdrop: 'static',
        //keyboard: false,
        show: true
    });

    $("#model-close-button").fadeIn(500);
}

function hide_movie_details_modal() {
    $("#the-whole").removeClass("active");
    $('#movie-details-model').modal("hide");
    $("#model-close-button").fadeOut(500);
    setTimeout(function () {

        $("#movie-details-model .modal-dialog").html("");
    }, 500);

}

function make_chart(ctx, type, data, white = false) {
    var l = data["labels"],
        d = data["values"]
    t = data["title"];

    var ds = (white) ? [{
        label: t,
        backgroundColor: 'rgba(255, 255, 255,0.3)',
        borderColor: 'rgba(205, 205, 205,0.3)',
        data: d,
        borderWidth: 1
    }] : [{
        label: t,
        //backgroundColor: 'rgba(255, 255, 255,0.3)',
        //borderColor: 'rgba(205, 205, 205,0.3)',
        data: d,
        borderWidth: 1
    }];
    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: type,

        // The data for our dataset
        data: {
            labels: l,


            datasets: ds
        },

        // Configuration options go here
        options: {}
    });
}

function notification(data) {
    if ($('#hover-effect-sound').length)
        $('#hover-effect-sound').remove();

    $("<audio></audio>").attr({
        'src': base_url + 'assets/audios/sound4.mp3',
        'volume': 0.4,
        'autoplay': 'autoplay'
    }).attr("id", "hover-effect-sound").appendTo("body");

    $("#notification").html(`
    <div class="notification-icon"></div>
    <div class="notification-message"><strong class="` + data["bc"] + `">` + data["big"] + `</strong> ` + data["message"] + `</div>`);
    $("#notification").addClass("active");
    setTimeout(function () {
        $("#notification").removeClass("active");
    }, 3100);
}

$(document).ready(function () {
    load_home();

    $("#login-form").submit(function (e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        login();
    });
    $(".nav-item.home").click(function () {
        load_home();
    });
    $("#mic-record-for-emo").click(function () {
        emo_recorder();
    });
    $("#movie-rightclick-menu .details-button").click(function () {
        load_movie_details(movie_id);
    });
    $("#movie-rightclick-menu .similar-button").click(function () {
        load_similar(movie_id);
    });
    $("#user-rightclick-menu .profile-button").click(function () {
        load_p_page(user_id);
    });
    $("#user-rightclick-menu .follow-button").click(function () {
        follow_request();
    });
    $("#model-close-button").click(function () {
        hide_movie_details_modal();
    });
    $('#movie-details-model').on('hidden.bs.modal', function (e) {
        hide_movie_details_modal();
      })
});