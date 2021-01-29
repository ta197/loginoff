<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!CModule::IncludeModule('iblock'))
    return;
$el = new CIBlockElement;

$res = [];
$res['iteration'] = "Итерация $iter началась";

for ($i = $number; $i < ($number + $STEP); $i++) {
    if($i>$COUNT)
        continue;     
    $arLoadProductArray =[
        "IBLOCK_ID" => $IBLOCK,
        "NAME" => "Тест материал ". $i,
        "CODE" => CUtil::translit(
            "Тест материал ". $i,
            "ru",
            ["replace_space"=>"-","replace_other"=>"-"]
            ),
        "PROPERTY_VALUES"=> [
            'CITY' => [
                'Город '.$i,
                'Страна '.$i,
                'Регион '.$i,
            ]
        ],
    ];
    if($ID = $el->Add($arLoadProductArray))
        $res['items'][$i] = "Добавлен элемент с ID ".$ID; 
    else
        $res['error'] = "Error: ".$el->LAST_ERROR;  
}
if(!$res['error']){
    $res['add'] = "Итерация $iter завершена";
}
echo json_encode($res);
?>
 
 

