<?php
abstract class ShopProductWriter {
    protected $products = [];

    public function addProduct(ShopProduct $shopProduct) {
        $this->products[] = $shopProduct;
    }

    abstract public function write();
}

class XmlProductWriter extends ShopProductWriter {
    public function write() {
        //...
    }
}

class TextProductWriter extends ShopProductWriter {
    public function write() {
        //...
    }
}
//------------------------------------------ DOCUMENTATION
//Введение абстрактных классов стало одним из главных изменений в РНР 5. А включение
//этой функции в список новых возможностей стало еще одним подтверждением
//растущей приверженности РНР объектно-ориентированному проектированию.
//Экземпляр абстрактного класса нельзя создать. Вместо этого в нем определяется
//(и. возможно, частично реализуется) интерфейс для любого класса, который может
//его расширить.

//В абстрактном классе вы можете создавать методы и свойства, как обычно, но
//любая попытка создать его экземпляр приведет к ошибке.
//РНР Fatal error:Cannot instantiate abstract class ShopProductWriter

//В большинстве случаев абстрактный класс будет содержать по меньшей мере
//один абстрактный метод. Как и класс, он описывается с помощью ключевого слова
//abstract. Абстрактный метод не может иметь реализацию в абстрактном классе.
//Он объявляется, как обычный метод, но объявление заканчивается точкой с
//запятой, а не телом метода.
//Создавая абстрактный метод, вы гарантируете, что его реализация будет доступной
//во всех конкретных дочерних классах, но детали этой реализации остаются
//неопределенными.

//Итак. в любом классе, который расширяет абстрактный класс, должны быть реализованы
//все абстрактные методы либо сам класс должен быть объявлен абстрактным.
//При этом в расширяющем классе должны быть не просто реализованы все
//абстрактные методы. но должна быть воспроизведена сигнатура этих методов. Это
//означает, что уровень доступа в реализующем методе не может быть более строгим,
//чем в абстрактном методе. Реализующему методу также должно передаваться такое
//же количество аргументов, как и абстрактному методу. а также в нем должны воспроизводиться
//все уточнения типов класса.

//В РНР 4 работу абстрактных классов моделировали с помощью методов, которые
//выводили предупреждающие сообщения или даже содержали операторы die(). Это
//заставляло программиста реализовывать абстрактные методы в производном классе,
//поскольку в противном случае сценарий переставал работать.
//class AbstractClass {
//    function abstractFunction() (
//      die("AbstractClass::abstractFunction() - абстрактная функция !");

//Проблема в таком подходе состоит в том, что абстрактная природа базового
//класса проверяется только в случае вызова абстрактного метода. В РНР 5 абстрактные
//классы проверяются еще на этапе синтаксического анализа, что намного безопаснее.