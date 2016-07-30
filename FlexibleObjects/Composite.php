<?php
abstract class Unit {

    function addUnit(Unit $unit) {
        throw new UnitException( get_class($this) . " относится к 'листьям'");
    }

    function removeUnit(Unit $unit) {
        throw new UnitException( get_class($this) . " относится к 'листьям'");
    }
    abstract function bombardStrength();
}

class UnitException extends Exception {}

class Archer extends Unit {

    function bombardStrength() {
        return 4;
    }
}

class LaserCannotUnit extends Unit {
    function bombardStrength() {
        return 44;
    }
}

class Army extends Unit{
    private $units = [];
    private $armies = [];

    function addUnit(Unit $unit) {
        if( in_array($unit, $this->units, true) ) {
            return;
        }
        array_push($this->units, $unit);
    }

    function removeUnit(Unit $unit) {
        $this->units = array_diff( $this->units, [$unit],
            function( $a, $b) { return ($a === $b) ? 0 : 1;} );
    }

    function bombardStrength() {
        $ret = 0;
        foreach($this->units as $unit) {
            $ret += $unit->bombardStrength();
        }
        return $ret;
    }

    function addArmy(Army $army) {
        array_push($this->armies, $army);
    }
}
//-------------------------------- RUN
$main_army = new Army();

$main_army->addUnit( new Archer() );
$main_army->addUnit( new LaserCannotUnit() );

$sub_army = new Army();
$sub_army->addUnit( new Archer() );
$sub_army->addUnit( new Archer() );
$sub_army->addUnit( new Archer() );

$main_army->addUnit( $sub_army );

print "Атакующая сила: {$main_army->bombardStrength()}<br>";
//-------------------------------- RESULT
//Атакующая сила: 60

//-------------------------------- DOCUMENTATION
//Шаблон Composite - это, наверное, самый экстремальный пример наследования,
//которое используется для обслуживания композиции. У этого шаблона простая,
//но поразительно изящная конструкция. Он также удивительно полезен. Но
//имейте в виду: из-за всех .этих преимуществ у вас может возникнуть искушение
//слишком часто его использовать.
//Шаблон Composite - это простой способ объединения, а затем и управления
//группами схожих объектов. В результате отдельный объект для клиентского кода
//становится неотличимым от коллекции объектов. В сущности, этот шаблон очень
//прост, тем не менее он может сбить с толку. Одна из причин этого - сходство струк-
//1УJJЫ классов в шаблоне с организацией его объектов. Иерархии наследования представляют
//собой деревья, корнем которых является суперкласс, а ветвями - специализированные
//подклассы. Дерево наследования классов, созданных с помощью
//шаблона Composite, предназначено для того. чтобы разрешить просrую генерацию
//объектов и их обход по дереву.
//Если вы еще не знакомы с этим шаблоном. то сейчас у вас есть полное право на
//то, чтобы почувствовать себя сбитым с толку. Чтобы проиллюстрировать, каким образом
//с отдельными объектами можно обращаться так же, как с наборами объектов,
//давайте воспользуемся аналогией. Имея такие ингредиенты, как крупа и мясо (или
//соя, в зависимости от предпочтений), можно произвести пищевой продукт, например
//колбасу. А затем с полученным результатом мы будем обращаться, как с единым
//объектом. Точно так же, как мы едим. готовим, покупаем или продаем мясо,
//мы можем есть, готовить, покупать или продавать колбасу. одним из компонентов
//которой является мясо. Мы можем взять колбасу и соединить ее с другими составными
//ингредиентами, чтобы сделать пирог, тем самым включив одну составную
//часть (композит) в большую составную часть (другой композит). Заметьте, мы обращаемся
//с наборами точно так же, как с их составными частями. Шаблон Composite
//помогает моделировать отношение между наборами и компонентами в нашем коде.

//преимушества данного шаблона.
//• Гибкость. Поскольку во всех элементах шаблона Composite используется общий
//  супертип, очень просто добавлять к проекту новые объекты-композиты
//  или "листья", не меняя более широкий контекст программы.
//• Простота. Клиентский код. использующий структуру Composite. имеет простой
//  интерфейс. Клиентскому коду не нужно делать различие между объектом.
//  состоящим из других объектов. и объектом-"листом" (за исключением случая
//  добавления новых компонентов). Вызов метода Army : : bomЬa rdSt rength ( ) может
//  стать причиной серии делегированных внутренних вызовов, но для клиентского
//  кода процесс и результат в точности эквивалентны тому, что связано
//  с вызовом Archer::bomЬardStrength() .
//• Неявна.я досягаемость. В шаблоне Composite объекты организованы в древовидную
//  структуру. В каждом композите содержатся ссылки на дочерний объект.
//  Поэтому операция над определенной частью дерева может иметь более
//  широкий эффект. Мы можем удалить один объект Army из его родительского
//  объекта Army и добавить к другому. Это простое действие осуществляется над
//  одним объектом. но в результате изменяется статус объектов U n i t , на которые
//  ссылается объект Army, а также статус их дочерних объектов.
//• Явная досягаемость. В древовидной структуре можно легко выполнить обход
//  всех ее узлов. Для получения информации нужно последовательно перебрать
//  все ее узлы либо выполнить преобразования. Очень эффективные методы
//  осуществления этих действий мы рассмотрим в следующей главе при изучении
//  шаблона Visitor.