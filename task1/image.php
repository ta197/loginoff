<?php
// Берем изображение и задаем как будет счетчик отображаться
$im = imageCreateFromJpeg('2017Animals___Cats_Little_kitten_with_sticking_out_tongue_119274_.jpg');
$text_color = imageColorAllocate($im, 255, 0, 0);

// Файл для хранения значения счётчика
$counter = 'count.txt';

// Считываем значение из файла-счетчика
if(file_exists($counter)){
    $current = file_get_contents($counter);
}else{
    $current = 0;
}
imageString($im, 5, 155, 10, ++$current, $text_color);

// Выводим картинку со значением счетчика, само значение пишем в файл-счётчик
header('Content-Type: image/jpeg');
if(imageJpeg($im)){
    file_put_contents($counter, $current);
}
imagedestroy($im);
?>