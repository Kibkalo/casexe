/**
 * Created by okibkalo on 21.08.14.
 */

function Login()
{
    Div_on_the_screen = document.getElementById('message');
    Function_request = function(){
        if(Div_on_the_screen.innerHTML == "1992"){
            location.reload();
        }
    }
    var login = document.getElementById('login').value;
    var password = document.getElementById('password').value;
    if(login.length != 0){
        if(password.length != 0){
            var sent_array = Array();
            sent_array.push("login=" + login);
            sent_array.push("password=" + password.replace(/,/g,"[KEPA]").replace(/=/g,"[POW]").replace(/'/g,"[TOP]"));
            var url = "Control/AJAX.php";
            var params = "Class=InfoIndex&method=Login&info="+ encodeURIComponent(sent_array);
            var style = 5;
            newSend(url, style, params);
        }else{
            document.getElementById('message').innerHTML = "Укажите пароль пользователя";
        }
    }else{
		document.getElementById('message').innerHTML = "Вкажіть логін користувача";
    }
}

function enter() {
    if(event.keyCode == 13){
        Login()
    }
}


