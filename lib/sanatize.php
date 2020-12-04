<?php
// Sanatize funcion from the text
function getPData($field){
    if (!isset($_POST[$field])){
        $data='';
    }elseif(is_array($_POST[$field])){
        $data = array();
        if (!empty($_POST[$field])){
            if($field=="delPros"){
                foreach ($_POST[$field] as $f){
                    array_push($data, (int)htmlentities($f, ENT_QUOTES, "UTF-8"));
                }
            }else{
                foreach ($_POST[$field] as $f){
                    array_push($data, htmlspecialchars(trim($f)));
                }
            }
        }
    } else {
        if ($field == "email") {
            $data = filter_var($_POST[$field], FILTER_SANITIZE_EMAIL);
        } else {
            $data = trim($_POST[$field]);
            $data = htmlspecialchars($data);
        }

    }
    return $data;
}

function getGData($field){
    if (!isset($_GET[$field])){
        $data="";
    } else {
        if($field=="page"){
            $data = trim($_GET[$field]);
            $data = (int)htmlentities($$field, ENT_QUOTES, "UTF-8");
        }else{
            $data = trim($_GET[$field]);
            $data = htmlspecialchars($data);
        }
    }
    return $data;
}



?>