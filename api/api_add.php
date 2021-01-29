<? require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/local/api/templates/api_add/header.php");
$APPLICATION->SetTitle("Загрузчик");

define("APIKEY", 'RUN2020');
define("IBLOCK_N", '123');
/*========================================
     проверки параметров запроса
=========================================*/

if($apikey!== APIKEY){
    echo '<div class="error">Перезайдите с правильным ключом!</div>';
    return;
}
if(!check($IBLOCK) || !check($STEP) || !check($COUNT)){
    echo '<div class="error">Исправьте ошибку в параметрах!</div>';
    return;
}
function check($param){
    return (is_int(IntVal($param)) && $param > 0) ? true : false;        
}
if($IBLOCK!== IBLOCK_N){
    echo '<div class="error">Неверный ID инфоблока!</div>';
    return;
}
/*========================================
подключение библиотеки js с расширением ajax
=========================================*/
CJSCore::Init(array('ajax'));
?>

<!-- ========================================
    контейнер для данных
========================================= -->
<div id="load" class="cont"></div>


<script type="text/javascript">
// ========================================
//  переменные
// ========================================= 
const IBLOCK = '<?php echo $IBLOCK;?>';
const COUNT = '<?php echo $COUNT;?>';
const STEP = '<?php echo $STEP;?>';
let number = 1;
let iter = 1;

// ========================================
//  сам запрос
// =========================================     
loader(number, iter);

function loader(number, iter) {
    if (number > COUNT) return;
    BX.ajax({
        url: '/local/api/loader.php', //php-файл для добавления элементов
        method: 'post',   
        data: {"number": number,
                "IBLOCK": IBLOCK,
                "STEP": STEP,
                "COUNT": COUNT,
                "iter": iter,
            },
        dataType: 'json',
        async: true,
        processData: true,
        emulateOnload: true,
        start: true,
        cache: false,
        onsuccess: function(data){
            data = BX.parseJSON(data);
            var list = '';
            for (var i in data.items) {
                list += '<li>' + i + ': ' + data.items[i] + '</li>';
            }
            BX('load').innerHTML 
                += '<ul><li>'
                + data.iteration 
                + '</li><li>Ход загрузки: <ul>' 
                + list
                + '</ul></li><li>' 
                + data.add
                + '</li></ul><hr>'
            ;
            ++iter;
            number = Number(number) + Number(STEP); 
            loader(number, iter);
        },
        onfailure: function(data){
            BX('load').innerHTML += data.error;
        }       
    });  
}
</script>

<?include_once($_SERVER["DOCUMENT_ROOT"]."/local/api/templates/api_add/footer.php");?>