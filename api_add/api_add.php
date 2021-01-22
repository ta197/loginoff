<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php");

define("APIKEY", 'RUN2020');

class Query{
    public static $query;
    public static $valide = true;
    public static $key;
    public function Set($IBLOCK, $STEP, $COUNT, $apikey){
        if($this->check($IBLOCK))
            static::$query['IBLOCK'] = $IBLOCK;
        if($this->check($STEP))
            static::$query['STEP'] = $STEP;
        if($this->check($COUNT))    
            static::$query['COUNT'] = $COUNT;
        static::$key = $apikey;
    }
    public function Get(){
       return static::$query;
    }
    public function GetKey(){
        return static::$key;
     }
    public function GetValuded(){
        return static::$valide;
     }
    public function check($param){
        if(is_int(IntVal($param)) && $param != 0){ 
            return true;
        }else{
            static::$valide = false; 
            return false;
        }
    }
}

(new Query)->Set($IBLOCK, $STEP, $COUNT, $apikey);
 
class Step1 extends CWizardStep
{
    function InitStep()
    {
        $this->SetTitle("Начало итерации");
        $this->SetStepID("step1");
        $this->SetNextStep("step2");
        $this->SetCancelStep("step3");
        $wizard =& $this->GetWizard();
        $key = Query::GetKey();
        $wizard->SetVar("key", $key); 
        $valude = Query::GetValuded();
        if($valude){
            $query = Query::Get();
            $wizard->SetVar("query", $query);  
        }
		$wizard->SetDefaultVars(
            [ 'iteration'=> 0, ]
        );
    }
    
    function ShowStep(){     
        $wizard = & $this->GetWizard();
        $iteration = IntVal($wizard->GetVar('iteration'));
        $query = $wizard->GetVar('query');
        $key = $wizard->GetVar('key');
        if($key!== APIKEY){
            $this->content = "Перезайдите с правильным ключом!<br/>";
            $this->SetNextStep("step3");
            return;
        }
        if(!$query){
            $this->content .= "Исправьте ошибку в параметрах!<br/>";
            $this->SetNextStep("step3");
            return;
        }
        $leftover = $wizard->GetVar('leftover');
        if($iteration == 0){        
                $this->content = '<h2>Получены следующие данные:</h2>';
                $this->content .= 'Ключ: '.$key.'<br/>';
                $this->content .= 'ID инфоблока для наполнения: '.$query['IBLOCK'].'<br/>';
                $this->content .= 'Шаг: по '.$query['STEP'].'<br/>';
                $this->content .= 'Предельное количество: '.$query['COUNT'].'<br/>';
                $this->content .= '<br/>========================================>>><br/>';
                $this->content .= 'Переход на первую итерацию <br/>';                
        }elseif($leftover > 0){
            $this->content = 'Переход на следующую итерацию (эта '.$iteration.').<br/>';
            if($leftover <= $query['STEP']) {
                $this->content .= 'Будут добавлены оставшиеся '.$leftover.' элементов(а).<br/>';
            }else{
                $this->content .= 'Будет добавлено еще '.$query['STEP'].' элементов(а).<br/>'; 
            }  
        }            
    }
    
    function OnPostForm(){
        $wizard = & $this->GetWizard();
        if($wizard->IsCancelButtonClick())
			return;       
        $query = $wizard->GetVar('query');							
        $leftover = $wizard->GetVar("leftover");
            if(null === $leftover){
                $leftover = $query['COUNT'];
            }
        if(!CModule::IncludeModule('iblock'))
            return;
        $el = new CIBlockElement;   
        $last = $wizard->GetVar('last');           
        $iteration = $wizard->GetVar("iteration");
        $t = ($leftover <= $query['STEP']) ? $leftover: $query['STEP'];
            
        for ($i = 1; $i <= $t; $i++) {
            $number = $last? ($last + $i) : $i;
            $leftover--;        
            $arLoadProductArray =[
                "IBLOCK_ID" => $query['IBLOCK'],
                "NAME" => "Тест материал ". $number,
                "CODE" => CUtil::translit(
                    "Тест материал ". $number,
                    "ru",
                    ["replace_space"=>"-","replace_other"=>"-"]
                    ),
                "PROPERTY_VALUES"=> [
                    'CITY' => [
                        'Город '.$number,
                        'Страна '.$number,
                        'Регион '.$number,
                    ]
                ],
            ];
            if($ID = $el->Add($arLoadProductArray))
                $res[$number] = "Добавлен элемент с ID ".$ID;
            else
                $res['error'] = "Error: ".$el->LAST_ERROR;      
        }
                
        $wizard->SetVar("leftover", $leftover);     
        $wizard->SetVar("iteration", $iteration + 1);    
        $wizard->SetVar("res", $res);
        $wizard->SetVar("last", $number); 
	}	  
}

class Step2 extends CWizardStep
{
    function InitStep()
    {
        $this->SetTitle("Результат добавления");
        $this->SetStepID("step2");
        $this->SetNextStep("step1");
        $this->SetCancelStep("step3");
    }
    function ShowStep(){     
        $wizard =& $this->GetWizard();
        $query = $wizard->GetVar('query');
        $res = $wizard->GetVar('res');
        $leftover = $wizard->GetVar('leftover');
        $this->content .= 'Добавилось '.count($res) .' элементов<br/>';
        $this->content .= '<br/>========== Результат добавления списком =====<br/>';
        foreach ($res as $k => $value) {
            $this->content .= $value.'. #НОМЕР заменен на '.$k.'<br/>';
        }
        $this->content .= '<br/>========== Дополнительно ================<br/>';
        $this->content .= 'Осталось до лимита '.$leftover.'<br/>';
        $last = $wizard->GetVar('last');  
        $this->content .= '#НОМЕР последнего элемента '.$last.'<br/>';
        if(isset($leftover) && $leftover<= 0){
            $this->content .= '<br/>========== Итого ================<br/>';
            $this->content .= "Все элементы размещены!";
            $this->SetNextStep("step3");
        }     
    }
}
class Step3 extends CWizardStep{
	
	function InitStep(){
		$this->SetStepID("step3");
		$this->SetTitle("Работа завершена!");		
		$this->SetCancelStep("step3");
		$this->SetCancelCaption("Готово");
	}
	
	function ShowStep(){
        
		$wizard = & $this->GetWizard();
        $iteration = IntVal($wizard->GetVar('iteration'));
        $query = $wizard->GetVar('query');
        $key = $wizard->GetVar('key');
        if(!$query)
            return;
        if($key!== APIKEY)
            return;
        $leftover = $wizard->GetVar('leftover');
        $this->content .='<h2>Итоговый отчет</h2>';
        $this->content .= 'Добавилось '.($query['COUNT'] - $leftover).' элементов <br/>';
        if($leftover && $query["COUNT"]){
            $this->content .= 'До исчерпания лимита осталось '.$leftover.' из '.$query["COUNT"].'<br/>';
        }   
        if($iteration)
            $this->content .= 'Прошло итераций '.$iteration.'<br/>';
	}
}

//Создаем мастер
$wizard = new CWizardBase("Наполнение инфоблока", $package = null);

//Добавляем шаги
$wizard->AddStep(new Step1);
$wizard->AddStep(new Step2);
$wizard->AddStep(new Step3);
//Выводим на экран
$wizard->Display();
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>