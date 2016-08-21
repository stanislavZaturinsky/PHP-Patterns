<?php

abstract class Unit
{
    protected $depth = 0;
    private $units = [];

    function accept(ArmyVisitor $visitor)
    {
        $method = "visit" . get_class($this);
        $visitor->$method($this);
        foreach ($this->units as $thisunit) {
            $thisunit->accept($visitor);
        }
    }

    protected function setDepth($depth)
    {
        $this->depth = $depth;
    }

    function getDepth()
    {
        return $this->depth;
    }

    function addUnit(Unit $unit)
    {
        foreach ($this->units as $thisunit) {
            if ($unit === $thisunit) {
                return;
            }
        }

        $unit->setDepth($this->depth + 1);
        $this->units[] = $unit;
    }
}

class Archer extends Unit
{
    function bombardStrength()
    {
        return 4;
    }
}

class Cavalry extends Unit
{
    function bombardStrength()
    {
        return 2;
    }
}

class LaserCannonUnit extends Unit
{
    function bombardStrength()
    {
        return 44;
    }
}

class TroopCarrierUnit extends Unit
{
    function bombardStrength()
    {
        return 20;
    }
}

class Army extends Unit
{
    private $units = [];
    private $armies = [];

    function addUnit(Unit $unit)
    {
        if (in_array($unit, $this->units, true)) {
            return;
        }
        array_push($this->units, $unit);
    }

    function removeUnit(Unit $unit)
    {
        $this->units = array_diff($this->units, [$unit],
            function ($a, $b) {
                return ($a === $b) ? 0 : 1;
            });
    }

    function bombardStrength()
    {
        $ret = 0;
        foreach ($this->units as $unit) {
            $ret += $unit->bombardStrength();
        }
        return $ret;
    }

    function addArmy(Army $army)
    {
        array_push($this->armies, $army);
    }

    function accept(ArmyVisitor $visitor)
    {
        foreach ($this->units as $thisunit) {
            $thisunit->accept($visitor);
        }
    }
}

abstract class ArmyVisitor
{
    abstract function visit(Unit $node);

    function visitArcher(Archer $node)
    {
        $this->visit($node);
    }

    function visitCavalry(Cavalry $node)
    {
        $this->visit($node);
    }

    function visitLaserCannonUnit(LaserCannonUnit $node)
    {
        $this->visit($node);
    }

    function visitTroopCarrierUnit(TroopCarrierUnit $node)
    {
        $this->visit($node);
    }

    function visitArmy(Army $node)
    {
        $this->visit($node);
    }
}

class TextDumpArmyVisitor extends ArmyVisitor
{
    private $text = "";

    function visit(Unit $node)
    {
        $txt = "";
        $txt .= get_class($node) . ": ";
        $txt .= "Огневая мощь: " . $node->bombardStrength() . "<br>";
        $this->text .= $txt;
    }

    function getText()
    {
        return $this->text;
    }
}

//-------------------------------- RUN
$main_army = new Army();
$main_army->addUnit(new Archer());
$main_army->addUnit(new LaserCannonUnit());
$main_army->addUnit(new Cavalry());

$textdump = new TextDumpArmyVisitor();
$main_army->accept($textdump);
print $textdump->getText();
//-------------------------------- RESULT
//Archer: Огневая мощь: 4
//LaserCannonUnit: Огневая мощь: 44
//Cavalry: Огневая мощь: 2
//-------------------------------- DOCUMENTATION
//Мы создаем объект Army. Поскольку Army - композит, у него есть метод addUnt(),
//который мы используем для добавления дополнительных объектов типа Unit.
//Затем мы создаем объект TextDumpArmyVisitor. Мы передаем его методу Army::accept().
//Метод accept() на ходу создает имя метода и вызывает TextDumpArmy
//Visitor::visitArmy(). В данном случае мы не обеспечили специальной обработки
//для объектов типа Army, поэтому вызов передается общему методу visit(). Методу
//visit() передается по ссылке наш объект Army. Он вызывает свои методы (включая
//вновь добавленный getDepth(), который сообщает всем, кому нужно знать, глубину
//вложения элемента в иерархии объекта), чтобы сгенерировать итоговые данные.
//Вызов visitArmy() выполнен, операция Army::accept() теперь вызывает по очереди
//метод accept() для своих дочерних объектов, передавая объект-посетителя.
//В результате класс ArmyVisitor посетит каждый объект на дереве.
//Добавив всего пару методов. мы создали механизм , посредством которого можно
//встроить новые функции в классы-композиты. не ухудшая их интерфейс и не используя
//много повторений кода обхода дерева.
//На некоторых клетках в нашей игре армии должны платить налоги. Сборщик налогов
//посещает армию и берет плату за каждый элемент (подразделение). который
//он находит. Разные подразделения должны платить разные суммы налогов. Здесь
//мы можем воспользоваться преимуществами специализированных методов в классе-
//посетителе.

//Проблемы шаблона Visitor
//Шаблон Visitor - это еще один шаблон, объединяющий в себе простоту и функциональность.
//Но при его использовании следует помнить о некоторых моментах.
//Хотя шаблон Visitor идеально приспособлен для использования с шаблоном
//Composite, на самом деле его можно применять с любым набором объектов. Так что
//можете использовать его, например, со списком объектов, где каждый объект сохраняет
//ссылку на его "братьев" (т.е . на элементы одного уровня на дереве).
//Однако экспортируя операции, вы рискуете "скомпрометировать" инкапсуляцию,
//т.е. вам может понадобиться показать внутреннее содержание посещенных
//объектов, чтобы позволить посетителям сделать с ними что-то полезное. Например,
//в нашем первом примере с Visitor мы были вынуждены обеспечить дополнительный
//метод для интерфейса Unit. чтобы предоставить информацию для объектов
//TextDumpArmyVisitor. С этой дилеммой мы уже сталкивались в шаблоне Observer.
//Поскольку сам процесс итерации отделен от операций, которые выполняют объекты-
//посетители, вы должны ослабить контроль в некоторой степени. Например,
//вы не можете легко создать метод visit(), который что-то делает и до, и после того,
//как выполняются итерации дочерних узлов. Один из способов решения - передать
//ответственность за выполнение итерации в объекты-посетители. Но проблема в
//том, что это может привести к дублированию кода обхода в каждом посетителе.
//По умолчанию я предпочитаю оставлять код обхода внутри посещенных классов,
//но экспортирование его даст вам одно особое преимущество. Вы сможете изменять
//способ обработки посещенных классов в зависимости от посетителя.