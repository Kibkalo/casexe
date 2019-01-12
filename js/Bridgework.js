
var Loading = "<center><img src='../Image/load.GIF'></center>";
var LoadingReports = "<center><img src='../../Image/load.GIF'></center>";
var Div_on_the_screen = false;

function newSend(url, send_num, params)
{
    if (Div_on_the_screen && (send_num == 1 || send_num == 10) && Div_on_the_screen.innerHTML == "") {
        Div_on_the_screen.innerHTML = Loading;
    }
    var request;
    if(window.XMLHttpRequest) {
        request = new XMLHttpRequest();
    } else if(window.ActiveXObject) {
        request = new ActiveXObject("Microsoft.XMLHTTP");
    } else {
        return;
    }
    if (!request) {
        alert("Error initializing XMLHttpRequest!");
    }

    request.open('POST', url, true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    request.onreadystatechange = getResponse(request, send_num);
    request.send(params);
}

function getResponse(request, send_num)
{
    return function()
    {
        if (request.readyState == 4) {
            if (request.status == 200) {
                if (send_num == 1){
                    onTheScreen(request.responseText);
                }
                if (send_num == 2){
                    onTheAlert(request.responseText);
                }
                if (send_num == 3){
                    onTheArea(request.responseText);
                }
                if (send_num == 4){
                    onTheAutocomplete(request.responseText);
                }
                if (send_num == 5){
                    onTheFunctionScreen(request.responseText);
                }
                if (send_num == 6){
                    onTheFunction(request.responseText);
                }

                if (send_num == 10){
                    onMessageScreen(request.responseText);
                }
                if (send_num == 11){
                    onFunctionOfMessageScreen(request.responseText);
                }
            } else if (request.status == 404) {
                alert("Сторінки не існує. Помилка 404.");
            } else if (request.status == 403) {
                alert("Доступ заборонено! Зверніться до адміністратора системи.");
            }
        }
    }
}

function onFunctionOfMessageScreen(request)
{
    var answer = request.split("#+#");
    if (answer[1] == "TRUE" || answer[1] == 1) {
        Div_on_the_screen.innerHTML = "";
        Function_request();
    } else {
        Div_on_the_screen.innerHTML = setMessage(answer[1], answer[0]);
    }
}

function onMessageScreen(request)
{
    var answer = request.split("#+#");
    if (answer[1] == "TRUE" || answer[1] == 1) {
        answer[1] = "Дані успішно збережені.";
    }
    Div_on_the_screen.innerHTML = setMessage(answer[1], answer[0]);
    Div_on_the_screen.scrollIntoView(true);
}

function onTheFunctionScreen(request)
{
    if (request == "destroy") {
        Div_on_the_screen.innerHTML = "Неудачно отправка запроса=(.";
    } else if(request == "TRUE" || request == 1) {
        Div_on_the_screen.innerHTML = "Дані успішно збережені.";
        Function_request();
    } else {
        Div_on_the_screen.innerHTML = request;
        Function_request();
    }
}

function onTheScreen(request)
{
    if (request == "destroy") {
        Div_on_the_screen.innerHTML = "Неудачно отправка запроса=(.";
    } else if(request == "TRUE" || request == 1) {
        Div_on_the_screen.innerHTML = "Дані успішно збережені.";
    } else {
        Div_on_the_screen.innerHTML = request;
    }
}

function onTheAlert(request)
{
    if (request !== 'TRUE') {
        alert(request);
    }
}

function onTheArea(request)
{
	$(Div_on_the_screen).htmlarea('html', request);
}

function onTheAutocomplete(request)
{
    var OBL=JSON.parse(request);
    var Obl=OBL.OBL;
    ArrAuto.length=0;
    ArrIdAuto.length=0;

    for (var i=0; i<Obl.length; i++)
    {
        ArrAuto[i]=Obl[i].name.replace("&quot;", "'");
        ArrIdAuto[Obl[i].name]=Obl[i].id;
    }

    var str = document.getElementById(NameInput).value.split(" ");
    if(str.length>1){
        document.getElementById(NameInput).value = str[1];
    }
    $(function(){
        $("#"+NameInput).autocomplete({
            source:ArrAuto,
            width:300
        });
    });
}

function onTheFunction(request)
{
    Function_request(request);
}
