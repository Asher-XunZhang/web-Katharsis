function signup() {
    var article = (document.getElementsByClassName("mainbody"))[0];
    article.classList.toggle("middle-flip");
    article.style.height="570px";
    article.style.width = "730px";
    if(article.classList.contains("Login")){
        article.classList.remove("Login");
    }
};
function login() {
    var article = (document.getElementsByClassName("mainbody"))[0];
    article.classList.toggle("middle-flip");
    article.style.height="440px";
    article.style.width = "400px";
    if (article.classList.contains("Register")){
        article.classList.remove("Register");
    }
};
function revise(obj){
    obj.style.border = "2px solid #3498db";
};
function showtips(num){
    $tip = "tip"+[num];
    var tip = (document.getElementsByClassName($tip))[0];
    tip.style.display = "block";
}
function hiddentips(num){
    $tip = "tip"+[num];
    var tip = (document.getElementsByClassName($tip))[0];
    tip.style.display = "none";
}
window.onload = function () {
    var main = (document.getElementsByTagName("main"))[0];
    if(main.classList.contains("confirm")){
        var confirm = (document.getElementsByClassName("email-window"))[0];
        confirm.style.display = "block";
        window.onclick = function(event) {
            if (event.target == confirm){
                confirm.style.display = "none";
            }
        }
    }else{
        var article = (document.getElementsByClassName("mainbody"))[0];
        if(article.classList.contains("mistakes")){
            var mistakes_window = (document.getElementsByClassName("mistakes-window"))[0];
            mistakes_window.style.display = "block";
        }
        window.onclick = function(event) {
            if (event.target == mistakes_window){
                mistakes_window.style.display = "none";
            }
        }
        if (article.classList.contains("Register")){
            signup();
        }else if(article.classList.contains("Login")){
            login;
        }
    }
    /************* fixed the footer on the bottom*************/
    $("footer").css({"top": $(document.body).outerHeight(true)+"px"});
}
