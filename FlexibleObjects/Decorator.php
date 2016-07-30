<?php
//1
//abstract class Tile {
//    abstract function getWealthFactor();
//}
//
//class Plains extends Tile {
//    private $wealthfactor = 2;
//
//    function getWealthFactor() {
//        return $this->wealthfactor;
//    }
//}
//
//abstract class TileDecorator extends Tile {
//    protected $tile;
//
//    function __construct( Tile $tile ) {
//        $this->tile = $tile;
//    }
//}
//
//class DiamondDecorator extends TileDecorator {
//    function getWealthFactor() {
//        return $this->tile->getWealthFactor() + 2;
//    }
//}
//
//class PollutionDecorator extends TileDecorator {
//    function getWealthFactor() {
//        return $this->tile->getWealthFactor() - 2;
//    }
//}
//-------------------------------- RUN
//$tile = new Plains();
//print $tile->getWealthFactor();// return 2;
//
//$tile = new DiamondDecorator( new Plains() );
//print $tile->getWealthFactor();// return 4;
//
//$tile = new PollutionDecorator( new DiamondDecorator( new Plains() ));
//print $tile->getWealthFactor();// return 0;

//2
class RequestHelper {}

abstract class ProcessRequest {
    abstract function process( RequestHelper $req );
}

class MainProcess extends ProcessRequest {
    function process( RequestHelper $req ) {
        print __CLASS__ . ": выполнение запроса<br>";
    }
}

abstract class DecorateProcess extends ProcessRequest {
    protected $processrequest;

    function __construct( ProcessRequest $pr ) {
        $this->processrequest = $pr;
    }
}

class LogRequest extends DecorateProcess {
    function process( RequestHelper $req ){
        print __CLASS__ . ": регистрация запроса<br>";
        $this->processrequest->process( $req );
    }
}

class AuthenticateRequest extends DecorateProcess {
    function process( RequestHelper $req ){
        print __CLASS__ . ": аутентификация запроса<br>";
        $this->processrequest->process( $req );
    }
}

class StructureRequest extends DecorateProcess {
    function process( RequestHelper $req ){
        print __CLASS__ . ": упорядочение данных запроса<br>";
        $this->processrequest->process( $req );
    }
}
//-------------------------------- RUN
$process = new AuthenticateRequest(
                new StructureRequest(
                    new LogRequest(
                        new MainProcess()
                    )));
$process->process( new RequestHelper() );
//-------------------------------- RESULT
//AuthenticateRequest: аутентификация запроса
//StructureRequest: упорядочение данных запроса
//LogRequest: регистрация запроса
//MainProcess: выполнение запроса

//-------------------------------- DOCUMENTATION 240
//В то время как шаблон Composite помогает создать гибкое представление из набора
//компонентов, шаблон Decorator использует сходную структуру. чтобы помочь
//модифицировать функции конкретных компонентов. И опять-таки, основа этого
//шаблона - важность композиции во время выполнения. Наследование - это хороший
//способ построения характеристик, определяемых родительским классом. Но
//это может привести к жесткому кодированию вариантов в иерархиях наследования.
//что, как правило, приводит к потере гибкости.
//
//Как и шаблон Composite, шаблон Decorator может показаться сложным для понимания.
//Важно помнить, что и композиция, и наследование вступают в действие
//одновременно. Поэтому LogRequest наследует свой интерфейс от ProcessRequest,
//но, в свою очередь, выступает в качестве оболочки для другого объекта типа ProcessRequest.
//Поскольку объект-декоратор формирует оболочку вокруг дочернего объекта,
//очень важно поддерживать интерфейс настолько неплотным, насколько это возможно.
//Если мы создадим базовый класс с множеством функций, то объекты-декораторы
//вынуждены будут делегировать эти функции всем общедоступным методам
//в объекте, который они содержат. Это можно сделать в абстрактном классе-декораторе,
//но в результате получится такая тесная связь, которая может привести к
//ошибкам.
//
//Некоторые программисты создают декораторы, не разделяющие общий тип с
//объектами, которые они модифицируют. Пока они работают в рамках того же ин-
//терфейса, что и данные объекты, описанная стратегия эффективна. При этом можно
//извлекать преимущества из того, что есть возможность использовать встроенные
//методы-перехватчики для автоматизации делегирования (реализуя метод _ cal1()
//для перехвата вызовов несуществующих методов и вызывая этот же метод на дочернем
//объекте автоматически). Но в результате вы теряете в безопасности, которую
//обеспечивает проверка типа класса. До сих пор в наших примерах клиентский код в
//своем списке аргументов мог требовать объект типа Tile или ProcessRequest и быть
//уверенным в его интерфейсе независимо от того, является ли этот объект сильно
//"декорированным".