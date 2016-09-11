<?php
//В отличие от языка С++, в РНР, как и в языке Java, не поддерживается множественное
//наследование. Однако эту проблему можно частично решить с помощью
//интерфейсов, как было показано в предыдущем разделе. Другими словами. для
//каждого класса в РНР может существовать только один родительский класс. Тем не
//менее в каждом классе можно реализовать произвольное количество интерфейсов.
//При этом данный класс будет соответствовать типам всех тех интерфейсов. которые
//в нем реализованы.
//Как видите. с помощью интерфейсов создаются новые типы объектов без их реализации.
//Но что делать, если вам нужно реализовать ряд общих методов для всей
//иерархии наследования классов? Для этой цели в РНР 5.4 было введено понятие
//трейтов
//По сути, трейты напоминают классы, для которых нельзя создать экземпляр
//объекта, но которые можно включить в другие классы. Поэтому любое свойство (или
//метод). определенное в трейте, становится частью того класса, в который этот трейт
//включен. При этом трейт изменяет структуру этого класса, но не меняет его тип.
//Можно считать трейты своего рода оператором include, действие которого распространяется
//только на конкретный класс.
//------------------------------------------ Без трейта
class ShopProduct {
    private $taxrate = 17;

    function calculateTax($price) {
        return (($this->taxrate / 100) * $price);
    }
}
$р = new ShopProduct();
print $p->calculateTax(100);
//Методу calculateTax() в качестве параметра $price передается цена товара. а
//он вычисляет налог с продаж на основе значения ставки, сохраненной во внутреннем
//свойстве $taxrate.
//Разумеется , доступ к методу calculateTax() будет у всех подклассов данного
//класса. Но что нам делать, если речь заходит о совершенно другой иерархии классов?
//Представьте себе класс UtilityService, который унаследован от другого класса
//Service. И если для класса UtilityService понадобится определить величину налога
//по точно такой же формуле, то нам ничего не остается другого, как просто целиком
//скопировать тело метода calculateTax(), как показано ниже.
//-------------------------------------------------------------------------------------------------76
abstract class Service {
    // Базовый класс для службы сервиса
}

class UtilityService extends Service {
    private $taxrate = 17;

    function calculateTax($price) {
        return (($this->taxrate / 100) * $price);
    }
}

$p = new ShopProduct();
print $p->calculateTax(100);

$u = new UtilityService();
print $u->calculateTax(100);
//------------------------------------------ Определение и использование трейтов
//Одной из целей объектно-ориентированного проектирования, которая красной
//нитью проходит через всю эту книгу, является устранение проблемы дублирования
//кода. Одним из возможных путей решения этой проблемы является вынесение общих
//фрагментов кода в отдельные повторно используемые стратегические классы
//(strategy class). Трейты также позволяют решить данную проблему. хотя, возможно,
//и менее элегантно, но, вне всякого сомнения, эффективно.
//В приведенном ниже примере я объявил простой трейт, содержащий метод
//calculateTax(), а затем включил его сразу в оба класса: ShopProduct и UtilityService.
trait PriceUtilities {
    private $taxrate = 17;

    function calculateTax($price) {
        return (($this->taxrate / 100) * $price);
    }
    //..
    //другие методы
}

class ShopProduct {
    use PriceUtilities;
}

abstract class Service {
    //Базовый класс для службы сервиса
}

class UtilityService extends Service {
    use PriceUtilities;
}

$p = new ShopProduct();
print $p->calculateTax(100);

$u = new UtilityService();
print $p->calculateTax(100);
//Как видите, трейт PriceUtilities объявляется с помощью ключевого слова trait.
//Тело трейта сильно напоминает тело обычного класса. В нем в фигурных скобках
//просто указывается набор методов (или, как вы увидите ниже, свойств). После объявления
//трейта PriceUtilities я могу его использовать при создании собственных
//классов. Для этого используется ключевое слово use, после которого указывает имя
//трейта. В результате, объявив и реализовав в одном месте метод calculateTax(), я
//могу его использовать в обоих классах: и в ShopProduct, и в UtilityService.
//------------------------------------------ С трейтом
//В класс можно включить несколько трейтов. Для этого их нужно перечислить через
//запятую после ключевого слова use. В приведенном ниже примере я определил
//и реализовал новый трейт IdentityTrait, а затем использовал его в своем классе
//наряду с трейтом PriceUtilities.
trait IdentityTrait {
    public function generateId() {
        return uniqid();
    }
}

trait PriceUtilities {
    private $taxrate = 17;

    function calculateTax($price) {
        return (($this->taxrate / 100) * $price);
    }
    //..
    //другие методы
}

class ShopProduct {
    use PriceUtilities, IdentityTrait;
}

$p = new ShopProduct();
print $p->calculateTax(100);
print $p->generateId();
//Перечислив оба трейта, PriceUtilities и IdentityTrait, после ключевого слова
//use, я сделал доступными методы calculateTax() и generateId() для класса
//ShopProduct. Это означает, что эти методы становятся членами класса ShopProduct.
//------------------------------------------ С трейтом и интерфейсом
//Несмотря на то что полезность применения трейтов не вызывает особых сомнений,
//они не позволяют изменить тип класса, в который были включены. Поэтому,
//если трейт IdentityTrait используется сразу в нескольких классах, у вас не будет
//общего типа, который можно было бы указать в уточнениях для сигнатур методов.
//К счастью, трейты можно успешно использовать вместе с интерфейсами . Мы можем
//определить интерфейс с сигнатурой метода generateId(), а затем указать, что
//в классе ShopProduct реализуются методы этого интерфейса.
interface IdentityObject {
    public function generateId();
}

trait IdentityTrait {
    public function generateId() {
        return uniqid();
    }
}

trait PriceUtilities {
    private $taxrate = 17;

    function calculateTax($price) {
        return (($this->taxrate / 100) * $price);
    }
    //..
    //другие методы
}

class ShopProduct implements IdentityObject {
    use PriceUtilities, IdentityTrait;
}
//Здесь, как и в предыдущем примере, в классе ShopProduct используется трейт
//IdentityTrait. Однако импортируемый с его помощью метод generateId() теперь
//также удовлетворяет требованиям интерфейса IdentityObject. А это означает, что
//мы можем передавать объекты ShopProduct тем методам и функциям, в описании
//аргументов которых используются уточнения типа объекта IdentityObject, как показано
//ниже.
function storeIdentityObject(IdentityObject $idobj) {
    // Работа с объектом типа IdentityObject
}

$p = new ShopProduct();
storeIdentityObject($p);
//------------------------------------------ Устранение конфликтов имен с помощью ключевого слова insteadof
//Возможность комбинирования трейтов является просто замечательной! Однако
//рано или поздно вы можете столкнуться с конфликтом имен. Например, что произойдет,
//если в обоих включаемых трейтах будет реализован метод calculateTax(),
//как показано ниже?
trait TaxTools {
    function calculateTax($price) {
        return 222;
    }
}

trait PriceUtilities {
    private $taxrate = 17;

    function calculateTax($price) {
        return (($this->taxrate / 100) * $price);
    }
    //..
    //другие методы
}

abstract class Service {
    //Базовый класс для службы сервиса
}

class UtilityService extends Service {
    use PriceUtilities, TaxTools;
}

$u = new UtilityService();
print $u->calculateTax(100);
//Поскольку в один классы мы включили два трейта, содержащие методы
//calculateTax(). интерпретатор РНР не сможет продолжить работу, так как он не
//знает, какой из методов нужно использовать

//Для устранения этой проблемы используется ключевое слово insteadof, как показано
//ниже.
trait TaxTools {
    function calculateTax($price) {
        return 222;
    }
}

trait PriceUtilities {
    private $taxrate = 17;

    function calculateTax($price) {
        return (($this->taxrate / 100) * $price);
    }
    //другие методы
}

abstract class Service {
    //Базовый класс для службы сервиса
}

class UtilityService extends Service {
    use PriceUtilities, TaxTools {
        TaxTools::calculateTax insteadof PriceUtilities;
    }
}

$u = new UtilityService();
print $u->calculateTax(100);
//Для того чтобы можно было применять директивы оператора use, нам нужно добавить
//к нему тело, которое помещается в фигурные скобки. Внутри этого блока используется
//конструкция с ключевым словом insteadof. Слева от него указывается
//полностью определенное имя метода. состоящее из имени трейта и имени метода.
//Они разделяются двумя двоеточиями, играющими в данном случае роль оператора
//определения зоны видимости. В правой части конструкции insteadof указывается
//имя трейта, метод которого с аналогичным именем должен быть заменен. Таким образом, запись
//TaxTools::calculateTax insteadof PriceUtilities;
//означает, что следует использовать метод calculateTax() трейта TaxTools в место
//одноименного метода трейта PriceUtilities.
//Поэтому при запуске приведенного выше кода будет выведено число 222, которое
//я ввел в код метода TaxTools::calculateTax().
//------------------------------------------ Псевдонимы для переопределенных методов трейта
//Выше мы уже убедились в том, что с помощью ключевого слова insteadof можно
//устранить конфликт имен методов, принадлежащих разным трейтам. Однако что
//делать, если вдруг понадобится вызвать в коде переопределенный метод трейта?
//Для этого используется ключевое слово as, которое позволяет назначить этому методу
//псевдоним. Как и в конструкции с ключевым словом insteadof, при использовании
//as нужно слева от него указать полностью определенное имя метода, а справа
//- псевдоним имени метода. В приведенном ниже примере метод calculateTax()
//трейта PriceUtilities был восстановлен под новым именем basicTax().
trait TaxTools {
    function calculateTax($price) {
        return 222;
    }
}

trait PriceUtilities {
    private $taxrate = 17;

    function calculateTax($price) {
        return (($this->taxrate / 100) * $price);
    }
    //другие методы
}

abstract class Service {
    //Базовый класс для службы сервиса
}

class UtilityService extends Service {
    use PriceUtilities, TaxTools {
        TaxTools::calculateTax insteadof PriceUtilities;
        PriceUtilities::calculateTax as basicTax;
    }
}

$u = new UtilityService();
print $u->calculateTax(100);
print $u->basicTax(100);
//------------------------------------------ RESULT
//222
//17
//Как видите, метод трейта PriceUtilities::calculateTax() стал частью класса
//UtilityService под именем basicTax().
//Кстати. здесь уместно отметить. что псевдонимы имен методов можно использовать
//даже тогда. когда нет никакого конфликта имен. Так. например. с помощью
//метода трейта вы можете реализовать абстрактный метод, сигнатура которого была
//объявлена в родительском классе или интерфейсе.
//------------------------------------------ Использование статических методов в трейте
//В большинстве примеров, которые мы рассматривали до сих пор. использовались
//статические методы. поскольку для их вызова не требуются экземпляры класса.
//Поэтому нам ничто не мешает поместить статический метод в трейт. В приведенном
//ниже примере я изменил описание свойства PriceUtilities::$taxrate и
//метода PriceUtilities::calculateTax() так. чтобы они стали статическими.
trait PriceUtilities {
    private static $taxrate = 17;

    static function calculateTax($price) {
        return ((self::$taxrate / 100) * $price);
    }
    //другие методы
}

abstract class Service {
    //Базовый класс для службы сервиса
}

class UtilityService extends Service {
    use PriceUtilities;
}

$u = new UtilityService();
print $u->calculateTax(100);
//------------------------------------------ RESULT
//17
//------------------------------------------ Доступ к свойствам базового класса
//После рассмотрения приведенных выше примеров у вас могло сложиться впечатление,
//что для работы с трейтами подходят только статические методы. И даже те
//методы трейта, которые не описаны как статические, являются по своей природе
//статическими. ведь так? Ну что же, вас ввели в заблуждение - к свойствам и методам
//базового класса также можно получить доступ.
trait PriceUtilities {
    function calculateTax($price) {
        return (($this->taxrate / 100) * $price);
    }
    //другие методы
}

abstract class Service {
    //Базовый класс для службы сервиса
}

class UtilityService extends Service {
    public $taxrate = 17;
    use PriceUtilities;
}

$u = new UtilityService();
print $u->calculateTax(100);
//Здесь я усовершенствовал трейт PriceUtilities так. чтобы из него можно было
//обращаться к свойству базового класса. И если вам кажется. что такой подход
//плох. то вы правы. Скажу больше - он вызывающе плох! Несмотря на то что обращение
//из трейтов к данным. расположенным в базовом классе, является обычной
//практикой, у нас не было весомых причин объявлять свойство $taxrate в классе
//UtilityService. Здесь не стоит забывать, что трейты могут использоваться во многих
//совершенно разных классах. И кто может дать гарантию (или даже обещание!),
//что в каждом базовом классе будет объявлено свойство $taxrate?
//    С другой стороны, будет просто замечательно, если вам удастся заключить договор
//с пользователем, в котором в частности говорится: "При использовании данного
//трейта вы обязаны предоставить в его распоряжение определенные ресурсы". По
//сути, здесь нам удалось достичь точно такого же эффекта. Дело в том, что в трейтах
//поддерживаются абстрактные методы.
//------------------------------------------ Определение абстрактных методов в трейтах
//В трейтах можно объявлять абстрактные методы точно так же, как и в обычных
//классах. При использовании такого трейта в классе в нем должны быть реализованы
//все объявленные в трейте абстрактные методы.
//Имея это в виду. я могу переписать предыдущий пример так. чтобы трейт заставлял
//использующий его класс предоставлять информацию о ставке налога.
trait PriceUtilities {
    function calculateTax($price) {
//      Гораздо лучший подход, поскольку нам точно известно,
//      что метод getTaxRate() будет реализован
        return (($this->getTaxRate() / 100) * $price);
    }

    abstract function getTaxRate();
    //другие методы
}

abstract class Service {
    //Базовый класс для службы сервиса
}

class UtilityService extends Service {
    use PriceUtilities;
    function getTaxRate() {
        return 17;
    }
}

$u = new UtilityService();
print $u->calculateTax(100);
//Объявив абстрактный метод getTaxRate() в трейте PriceUtilities, я вынудил
//программиста обеспечить его реализацию в классе UtilityService.
//Разумеется, поскольку в РНР не накладывается каких-либо жестких ограничений
//на тип возвращаемого значения, нельзя быть точно уверенным в том, что в методе
//UtilityService::calculateTax() мы получим от метода getTaxRate() корректное
//значение. Этот недостаток можно преодолеть, поместив в код операторы. выполняющие
//все возможные виды проверок, но тем самым мы не достигнем поставленной
//цели. Здесь, вероятнее всего, будет вполне достаточно указать программисту клиентского
//кода, что при реализации затребованных нами методов нужно возвратить
//значение заданного типа.
//------------------------------------------ Изменение прав доступа к методам трейта
//Разумеется, ничто не может вам помешать объявить методы трейта открытыми
//(puЬlic), защищенными (private} или закрытыми (protected}. Тем не менее вы
//можете также изменить эти атрибуты доступа к методам прямо внутри класса, в
//котором используется трейт. Выше было показано, как с помощью ключевого слова
//a s можно назначить мето.цу псевдоним. Если справа от этого ключевого слова указать
//новый модификатор доступа. то вместо назначения мето.цу псевдонима будет
//изменен его атрибут доступа.
//Давайте в качестве примера представим, что вы хотите использовать метод
//calculateTax() только внутри класса UtilityService и вам не нужно, чтобы этот
//метод можно было вызвать из клиентского кода. Внесите изменения в оператор use,
//как показано ниже.
trait PriceUtilities {
    function calculateTax($price) {
        return (($this->getTaxRate() / 100) * $price);
    }

    abstract function getTaxRate();
    //другие методы
}

abstract class Service {
    //Базовый класс для службы сервиса
}

class UtilityService extends Service {
    use PriceUtilities {
        PriceUtilities::calculateTax as private;
    }
    private $price;

    function __construct($price) {
        $this->price = $price;
    }

    function getTaxRate() {
        return 17;
    }

    function getFinalPrice() {
        return ($this->price + $this->calculateTax($this->price));
    }
}

$u = new UtilityService(100);
print $u->getFinalPrice();
//Для того чтобы закрыть доступ к методу calculateTax() извне класса UtilityService,
//после ключевого слова as в операторе use был указан модификатор private.
//В результате доступ к этому методу стал возможен только из метода getFinalРriсе().
//Теперь при попытке обращения к методу calculateTax() извне класса, например так:
//$u = new UtilityService(100);
//print $u->calculateTax();
//Fatal Error