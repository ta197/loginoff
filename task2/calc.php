<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="utf-8">
    <title>Калькулятор</title>
  </head>
 <body>
  <p><b>Калькулятор</b></p>
 
    <form method="POST">
      <p>
        <input type="text"  name="operand1" value="<?=$valueFirst; ?>">
          <select size="1" name="operation">
            <option selected value="summa"> + </option>
            <option  value="subtraction"> - </option>
            <option value="multiplication"> * </option>
            <option value="division"> / </option>
          </select>
        <input type="text" name="operand2" value="<?=$valueSecond; ?>">
        <input type="submit" value="Считать" >
        <input type="hidden" name="list" value="<?php echo implode("|", $list); ?>">
      </p>
    </form>

 <?php 
  foreach(array_reverse($list) as $v){   
     echo $v;
  }
 ?>
  
 </body>
</html>
 