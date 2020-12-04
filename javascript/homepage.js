$(document).ready(function(){
    $("td").find("[type=checkbox]").click(function (){
        if($(this).prop('checked')){
            $(this).prop('checked', false);
            $(this).parents("tr").removeClass("selected");
        }else{
            $(this).prop('checked', true);
            $(this).parents("tr").addClass("selected");
        }
    });
    $("tr").click(function (){
        if($(this).find("[type=checkbox]").prop('checked')){
            $(this).find("[type=checkbox]").prop('checked', false);
        }else{
            $(this).find("[type=checkbox]").prop('checked', true);
        }

        $(this).toggleClass("selected");
    });
});
function revise(obj){
    obj.style.border = "2px solid #3498db";
};

window.onload=function (){
    /************* fixed the footer on the bottom*************/
    $("footer").css({"top": $(document.body).outerHeight(true)+"px"});
}