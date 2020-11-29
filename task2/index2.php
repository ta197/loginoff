<?php
if (!$list) {
    $res ='';
    $list = [];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'):
    $list = explode("|", $_POST["list"]);
    
    if(count($list)>10) array_shift($list);
    
    if(is_numeric($_POST["operand1"])){
        $operand1 = $_POST["operand1"];
    }else{
        $list[] = "Не задан первый операнд!". "</br>"; 
    }
    
    if(is_numeric($_POST["operand2"])){
        $operand2 = $_POST["operand2"];
    }else{
        $list[] = "Не задан второй операнд!". "</br>"; 
    }
    
    if(!isset($_POST["operation"])){
        $list[] = "Не задана операция". "</br>";
    } elseif(isset($operand1) && isset($operand2)){ 
           
        switch($_POST["operation"]){
            case summa: 
                $res = $operand1 + $operand2;
                $list[]= "$operand1 + $operand2 = $res". "</br>";
                break;
            case subtraction: 
                $res = $operand1 - $operand2; 
                $list[]= "$operand1 - $operand2 = $res". "</br>";
                break;
            case multiplication: 
                $res = $operand1 * $operand2; 
                $list[]= "$operand1 * $operand2 = $res". "</br>";
                break;
            case division: 
                if($operand2== 0){
                    $list[] = "На 0 не делим!". "</br>"; 
                    break;
                }
                $res = $operand1 / $operand2; 
                $list[]= "$operand1 : $operand2 = $res". "</br>";
                break;
            default: 
                $list[] = "Операция не из списка". "</br>";
        }
    } 
endif;

if(is_numeric($res)){
    $valueFirst =  $res;
    $valueSecond =  '';
}elseif(is_numeric($operand1)){ 
    $valueFirst = $operand1;
}elseif(is_numeric($operand2)){ 
    $valueSecond = $operand2;
} 

include 'calc.php';
?>
