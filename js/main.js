/**
 * Created by okibkalo on 10.01.19.
 */

function game(obj)
{
    Div_on_the_screen = document.getElementById('showDiv');
    document.getElementById('showDiv').innerHTML = Loading;
    Function_request = block;
    var sent_array = Array();
    sent_array.push("id=" + obj.id);
    var url = "http://" + location.hostname + "/Control/AJAX.php";
    var params = "Class=InfoIndex&method=setGame&info="+ encodeURIComponent(sent_array);
    var style = 5;
    newSend(url, style, params);
}

function block()
{
    Div_on_the_screen = document.getElementById('block');
    document.getElementById('block').innerHTML = Loading;
    var sent_array = Array();
    var url = "http://" + location.hostname + "/Control/AJAX.php";
    var params = "Class=InfoIndex&method=getBlock&info="+ encodeURIComponent(sent_array);
    var style = 1;
    newSend(url, style, params);
}

function trans(obj)
{
    Div_on_the_screen = document.getElementById('showTrans');
    document.getElementById('showTrans').innerHTML = Loading;
    Function_request = block;
    var sent_array = Array();
    sent_array.push("id=" + obj.id);
    sent_array.push("in="+document.getElementById(obj.id + '_in').value);
    var url = "http://" + location.hostname + "/Control/AJAX.php";
    var params = "Class=InfoIndex&method=trans&info="+ encodeURIComponent(sent_array);
    var style = 5;
    newSend(url, style, params);
}

function sell(obj)
{
    Div_on_the_screen = document.getElementById('showTrans');
    document.getElementById('showTrans').innerHTML = Loading;
    Function_request = block;
    var sent_array = Array();
    sent_array.push("id=" + obj.id);
    var url = "http://" + location.hostname + "/Control/AJAX.php";
    var params = "Class=InfoIndex&method=sell&info="+ encodeURIComponent(sent_array);
    var style = 5;
    newSend(url, style, params);
}

function bank()
{
    Div_on_the_screen = document.getElementById('showPiece');
    document.getElementById('showPiece').innerHTML = Loading;
    Function_request = block;
    var sent_array = Array();
    sent_array.push("piece="+document.getElementById('piece').value);
    var url = "http://" + location.hostname + "/Control/AJAX.php";
    var params = "Class=InfoIndex&method=bank&info="+ encodeURIComponent(sent_array);
    var style = 5;
    newSend(url, style, params);
}