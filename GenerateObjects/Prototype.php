<?php
class Sea {}
class EarthSea extends Sea {}
class MarsSea extends Sea {}

class Plains {}
class EarthPlains extends Plains {}
class MarsPlains extends Plains {}

class Forest {}
class EarthForest extends Forest {}
class MarsForest extends Forest {}

class TerrainFactory {
    private $sea;
    private $forest;
    private $plains;

    function __construct(Sea $sea, Plains $plains, Forest $forest) {
        $this->sea = $sea;
        $this->plains = $plains;
        $this->forest = $forest;
    }

    function getSea() {
        return clone $this->sea;
    }

    function getPlains() {
        return clone $this->plains;
    }

    function getForest() {
        return clone $this->forest;
    }
}
//-------------------------------- RUN
$factory = new TerrainFactory(new EarthSea(), new EarthPlains(), new EarthForest());
print_r($factory->getSea());
print_r($factory->getPlains());
print_r($factory->getForest());
//-------------------------------- RESULT
//EarthSea Object ( ) EarthPlains Object ( ) EarthForest Object ( )

//-------------------------------- DOCUMENTATION
//Работая с шаблонами Abstract Factory и Factory Method , мы должны решить в
//определенный момент, с каким конкретно создателем хотим работать. Вероятно,
//это можно осуществить путем анализа значения некоторого флага. Поскольку так
//или иначе мы должны это сделать, почему бы просто не создать класс фабрики,
//хранящий конкретные продукты и размножающий их во время инициализации?
//Таким образом мы можем избавиться от нескольких классов и, как мы вскоре увидим,
//воспользоваться другими преимуществами.

//Как видите, здесь м ы загружаем в экземпляр конкретной фабрики типа Terrain
//F a c t o r y экземпляры объектов наших продуктов. Когда в клиентском коде вызывается
//метод g e t S e a ( ) , ему возвращается клон объекта S e a , который мы поместили в
//кеш во время инициализации. В результате мы не только сократили пару классов,
//но и достигли определенной гибкости. Хотите, чтобы игра происходила на новой
//планете с морями и лесами. как на Земле, и с равнинами, как на Марсе? Для этого
//не нужно писать новый класс создателя - достаточно просто изменить набор классов,
//который мы добавляем в T e r ra i n F a c t o r y .
//$ factory = n e w TerrainFactory (new EarthSea ( ) ,
//                                  new MarsPlains ( ) ,
//                                  new EarthForest ( ) ) ;
//Итак. шаблон Prototype позволяет пользоваться преимуществами гибкости, которые
//предоставляет композиция.