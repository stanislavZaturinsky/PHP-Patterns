<?php
namespace woo\base;

abstract class Registry {
    abstract protected function get($key);
    abstract protected function set($key, $val);
}

class RequestRegistry extends Registry {
    private $values = [];
    private static $instance = null;

    private function __construct() {}

    static function instance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function get($key) {
        if(isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }

    protected function set($key, $val) {
        $this->values[$key] = $val;
    }

    static function getReguest() {
        $inst = self::instance();
        if(is_null($inst->get('request'))) {
            $inst->set('request', new \woo\controller\Request());
        }
        return $inst->get('request');
    }
}

class SessionRegistry extends Registry {
    private static $instance = null;

    private function __construct() {
        session_start();
    }

    static function instance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function get($key) {
        if(isset($_SESSION[__CLASS__][$key])) {
            return $_SESSION[__CLASS__][$key];
        }
        return null;
    }

    protected function set($key, $val) {
        $_SESSION[__CLASS__][$key] = $val;
    }

    function setDSN($dsn) {
        self::instance()->set('dsn', $dsn);
    }

    function getDSN() {
        return self::instance()->get('dsn');
    }
}

class ApplicationRegistry extends Registry {
    private static $instance = null;
    private $freezedir = 'data';
    private $values = [];
    private $mtimes = [];

    private function __construct() {}

    static function instance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function get($key) {
        $path = $this->freezedir . DIRECTORY_SEPARATOR . $key;
        if(file_exists($path)) {
            clearstatcache();
            $mtime = filemtime($path);
            if(!isset($this->mtimes[$key])) {
                $this->mtimes[$key] = 0;
            }
            if($mtime > $this->mtimes[$key]) {
                $data = file_get_contents($path);
                $this->mtimes[$key] = $mtime;
                return ($this->values[$key] = unserialize($data));
            }
        }
        if(isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }

    protected function set($key, $val) {
        $this->values[$key] = $val;
        $path = $this->freezedir . DIRECTORY_SEPARATOR . $key;
        file_put_contents($path, serialize($val));
        $this->mtimes[$key] = time();
    }

    static function getDSN() {
        return self::instance()->get('dsn');
    }

    static function setDSN($dsn) {
        self::instance()->set('dsn', $dsn);
    }

    static function getRequest() {
        $inst = self::instance();
        if(is_null($inst->request)) {
            $inst->request = new \woo\controller\Request();
        }
    }
}

class MemApplicationRegistry extends Registry {
    private static $instance = null;
    private $values = [];
    private $id;

    private function __construct() {}

    static function instance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function get($key) {
        return \apc_fetch($key);
    }

    protected function set($key, $val) {
        return \apc_store($key, $val);
    }

    static function getDSN() {
        return self::instance()->get('dsn');
    }

    static function setDSN($dsn) {
        self::instance()->set('dsn', $dsn);
    }
}
//-------------------------------- DOCUMENTATION
//В базовом классе определены два защищенных (protected) метода, get() и set().
//Они недоступны клиентскому коду. потому что мы хотим задавать обязательный
//тип для операций получения (get) и установки (set).

//В классе ApplicationRegistry используется сериализация для сохранения и восстановления
//значений отдельных свойств. В функции get() проверяется существование соответствующего
//файла, содержащего сериализованные значения. Если этот файл существует
//и был изменен со времени последнего чтения, то в методе считывается его
//содержимое и восстанавливается значение переменной. Поскольку нерационально
//сохранять в отдельном файле значения каждой сериализуемой переменной, можно
//применить другой подход - поместить значения всех свойств в один файл сохранения.
//В методе set() изменяется значение свойства, на которое указывает переменная
//$key, и локально, и в файле сохранения. В нем обновляется значение свойства
//$mtimes. Это массив с информацией о времени изменения, который используется
//для проверки файлов сохранения. Затем, при вызове метода get(), значение
//$mtimes сравнивается со временем модификации файла, чтобы узнать, был ли он
//изменен со времени последней записи этого объекта.
//Обратите внимание, что представленный здесь метод getRequest() идентичен
//одноименному методу класса RequestRegistry, относящемуся к уровню запроса, который
//был рассмотрен выше. В обоих случаях не существует соответствующего метода
//setRequest(). Здесь мы отошли от практики предоставления сторонним объектам
//возможности создавать собственные версии объектов Request и записывать их
//в реестр. Вместо этого мы предложили механизм, благодаря которому системный
//реестр становится единым источником общего объекта Request. и предоставили гарантию,
//что только один экземпляр этого объекта будет доступен всем элементам
//нашего приложения.
//Описанный выше подход также крайне полезен для целей тестирования.
//Благодаря общему реестру мы можем подменить объект Request перед запуском
//тестовой версии приложения. Это позволит нам создать различные внешние условия,
//на которые должна отреагировать программа, и посмотреть на получаемый
//результат. Также обратите внимание, что объект Request сохраняется в объекте
//ApplicationRegistry в обычном свойстве $request и не сохраняется в файле. Все
//дело в том , что нам не нужно, чтобы именно этот объект сохранял свое состояние
//между запросами!

//Результаты
//Поскольку и SessionRegistry, и ApplicationRegistry сериализуют данные в
//файл, важно еще раз сформулировать очевидный факт: объекты, извлекаемые в
//разных запросах, являются идентичными копиями и не ссьлаются на один и тот же
//объект. Это не должно иметь значения для SessionRegistry, потому что к объекту в
//каждом случае обращается один и тот же пользователь. Но для ApplicationRegistry
//это может быть серьезной проблемой. Сохраняя данные беспорядочно, вы можете
//оказаться в ситуации, в которой два процесса будут конфликтовать. Рассмотрим
//описанную ниже последовательность действий.

//Процесс l извлекает объект
//Процесс 2 извлекает объект
//Процесс l изменяет объект
//Процесс 2 изменяет объект
//Процесс l сохраняет объект
//Процесс 2 сохраняет объект

//Изменения, выполненные Процессом1, затираются в результате сохранения
//Процессом2. Если вы действительно хотите создать совместно используемое пространство
//для данных, реализуйте в классе ApplicationRegistry схему взаимоблокировки
//для предотвращения подобных противоречий. Существует и альтернативный
//вариант: рассматривать класс ApplicationRegistry в большей степени как
//ресурс "только для чтения". Именно таким образом я использую этот класс в последующих
//примерах в данной главе. Первоначально в нем устанавливаются данные,
//и после этого взаимодействие с ним происходит по принципу "только чтение". Если
//файл хранилища не найден, то в коде вычисляются новые значения и записываются
//в этот файл. Следовательно, перезагрузку данных конфигурации можно инициировать
//только путем удаления файла хранилища. Более того, вы можете усовершенствовать
//класс так, чтобы он работал в режиме "только для чтения".
//Еще один важный момент, о котором нужно помнить, - не каждый объект подходит
//для сериализации. В частности, если вы будете сохранять ресурсы любого
//типа (например, дескриптор подключения к базе данных), то он не будет сериализован.
//Вам придется разработать стратегии, обрабатывающие такой дескриптор во
//время сериализации и восстанавливающие его при десериализации.
//Итак, какую же стратегию выбрать? На практике я почти всегда использую самый
//простой вариант - реестр, работающий только с запросами. Разумеется, в
//разрабатываемой программной системе я всегда использую только один тип реестра.
//Это позволяет избежать некоторых трудноуловимых ошибок! Механизм кеширования,
//рассмотренный выше в примере с классом ApplicationRegistry. позволяет
//преодолеть недостатки шаблона Front Controller, заключающиеся в высоких
//накладных расходах. связанных с синтаксическим анализом запутанного файла
//конфигурации в каждом запросе. В реальных приложениях механизм кеширования
//следует реализовать отдельно, чтобы максимально упростить реестр и ограничить
//его функциональные возможности только запросами. Тем не менее в данном случае
//класс ApplicationRegistry служит поставленным мною целям, поэтому я продолжу
//с ним работать.

//Хотя сериализация в РНР довольно эффективна, вы должны быть внимательны
//в отношении того, что сохраняете. Простой на вид объект может содержать ссылку
//на огромный набор объектов, полученных из базы данных.
//Объекты типа Registry делают свои данные глобально доступными. Это означает,
//что любой класс, действующий как клиент для реестра. будет проявлять зависимость,
//не объявленную в его интерфейсе. Это может стать серьезной проблемой.
//если слишком много данных в вашей системе будут зависеть от объектов типа
//Registry. Поэтому объекты типа Registry лучше всего использовать сравнительно
//редко, для четко определенного набора элементов данных.