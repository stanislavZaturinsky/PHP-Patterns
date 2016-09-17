<?php
//В РНР 5 было введено понятие исключения, представляющее собой совершенно
//другой способ обработки ошибок. Я хочу сказать - совершенно другой для РНР. Но
//если у вас есть опыт работы с Java или С++, то исключения покажутся вам знакомыми
//и близкими. Использование исключений позволяет решить все проблемы, которые
//я описывал в данном разделе.
//Исключение - это специальный объект, который является экземпляром встроенного
//класса Exception(или производного от него класса). Объекты типа Exception
//предназначены для хранения информации об ошибках и выдачи сообщений о них.
//Конструктору класса Exception передаются два необязательных аргумента:
//строка сообщения и код ошибки. В этом классе существуют также некоторые полезные
//методы для анализа ошибочной ситуации(таблица).

//Таблица
//getMessage()          Получить строку сообщения, переданную конструктору
//getCode()             Получить код ошибки (целое число), который был передан конструктору
//getFile()             Получить имя файла, в котором было сгенерировано исключение
//getLine()             Получить номер строки, в которой было сгенерировано исключение
//getPrevious()         Получить вложенный объект типа Exception
//getTrace()            Получить многомерный массив, отслеживающий вызовы метода, которые
//                      привели к исключению, в том числе имя метода, класса, файла и
//                      значение аргумента
//getTraceAsString()    Получить строковую версию данных, возвращенных методом getTrace()
//_toString()           Вызывается автоматически, когда объект Exception используется в
//                       контексте строки. Возвращает строку, описывающую подробности исключения

//Класс Exception крайне полезен для поиска сообщения об ошибке и информации
//для отладки (в этом отношении особенно полезны методы getTrace() и getTraceAsString()).
//На самом деле класс Exception почти идентичен классу PEAR_Error, который
//мы обсуждали выше. Но в нем сохраняется меньше информации об исключениях.
//чем есть на самом деле.

//Создание подклассов класса Exception
//Вы можете создать классы, расширяющие класс Exception. точно так же. как это
//делается для любого другого определенного пользователем класса. Существуют две
//причины, по которым возникает необходимость это сделать. Во-первых. можно расширить
//функциональность класса. Во-вторых. производный класс определяет новый
//тип класса, который поможет при обработке ошибок.
//В сущности, для одного оператора try вы можете определить столько операторов
//catch, сколько нужно. То, какой конкретно оператор catch будет вызван. будет зависеть
//от типа сгенерированного исключения и указанного уточнения типа класса в
//списке аргументов. Давайте определим некоторые простые классы, расширяющие
//класс Exception.

class XmlException extends Exception {
    private $error;

    function __construct(LibXMLError $error) {
        $shortfile = basename($error->file);
        $msg  = "[{$shortfile}, строка {$error->line}, ";
        $msg .= "колонка {$error->column}] {$error->message}";
        $this->error = $error;
        parent::__construct($msg, $error->code);
    }

    function getLibXmlError() {
        return $this->error;
    }
}

class FileException extends Exception {}
class ConfException extends Exception {}

//Объект типа LibXmlError создается автоматически, когда средства SimpleXml
//обнаруживают поврежденный ХМL-файл. У него есть свойства message и code. и
//он напоминает класс Exception. Мы пользуемся преимуществом этого подобия и
//используем объект LibXmlError в классе XmlException. У классов FileException
//и ConfException не больше функциональных возможностей, чем у подкласса
//Exception. Теперь мы можем использовать эти классы в коде и подкорректировать
//оба метода, construct() и write().

class ConfException extends Exception {
    function __construct($file) {
        $this->file = $file;
        if(!file_exists($file)) {
            throw new FileException("Файл '$file' не существует");
        }

        $this->xml = simplexml_load_file($file, null, LIBXML_NOERROR);
        if(!is_object($this->xml)) {
            throw new XmlException(libxml_get_last_error());
        }

        print gettype($this->xml);
        $matches = $this->xml->xpath("/conf");
        if(!count($matches)) {
            throw new ConfException("Корневой элемент сonf не найден.");
        }
    }

    function write() {
        if(!is_writable($this->file)) {
            throw new FileExceprion("Файл '{$this->file}' недоступен для записи");
        }
        file_put_contents($this->file, $this->xml->asXML());
    }
}

//Meтoд _ construct( ) генерирует исключения типа XmlException, FileException
//или ConfException в зависимости от вида ошибки. которую о н обнаружит. Обратите
//внимание на то, что методу simplexml_load_file() передается флажок LIBXML_NOERROR.
//Это блокирует выдачу предупреждений внутри класса и оставляет программисту свободу
//действий для их последующей обработки с помощью класса XmlException. Если обнаружится
//поврежденный ХМL-файл, то метод simplexml_load_file() уже н е возвратит объект типа
//SimpleXMLElement. Благодаря классу XmlException в клиентском коде можно будет легко
//узнать причину ошибки, а с помощью метода libxmlget_last_error() - все подробности этой ошибки.
//Метод write() генерирует исключение типа FileException, если свойство $file
//указывает на файл, недоступный для записи.
//Итак. мы установили, что мeтoд _construct() может генерировать одно из трех
//возможных исключений. Как мы можем этим воспользоваться? Ниже приведен
//пример кода. в котором создается экземпляр объекта Conf().

class Runner {
    static function init() {
        try {
            $conf = new Conf(dirname(__FILE__) . "/conf01.xml");
            print "user: " . $conf->get('user') . "\n";
            print "host: " . $conf->get('host') . "\n";
            $conf->set("pass", "newpass");
            $conf->write();
        } catch(FileException $e) {
            //Файл не существует либо недоступен для записи
        } catch(XmlException $e) {
            //Поврежденный XML-файл
        } catch(ConfException $e) {
            //Некорректный формат XML-файл
        } catch(Exception $e) {
            //Ловушка: этот код не должен никогда вызываться
        }
    }
}

//В этом примере мы предусмотрели оператор catch для каждого типа класса
//ошибки. То, какой оператор будет вызван, зависит от типа сгенерированного исключения.
//При этом будет выполнен первый подходящий оператор. Поэтому помните:
//самый общий тип нужно размещать в конце, а самый специализированный -
//в начале списка операторов catch. Например, если бы вы разместили оператор
//catch для обработки исключения типа Exception перед операторами для обработки
//исключений типа XmlException и ConfException, ни один из них никогда не был бы
//вызван. Причина в том, что оба исключения относятся к типу Exception и поэтому
//будут соответствовать первому оператору.
//Первый оператор catch (FileException) вызывается, если есть проблема с файлом
//конфигурации (если этот файл не существует или в него нельзя ничего записать).
//Второй оператор catch (XmlException) вызывается, если происходит ошибка
//при синтаксическом анализе ХМL-файла (например. если какой-то элемент не закрыт).
//Третий оператор catch (ConfException) вызывается, если корректный в плане
//формата ХМL-файл не содержит ожидаемый корневой элемент conf. Последний
//оператор catch (Exception) не должен вызываться, потому что наши методы генерируют
//только три исключения, которые обрабатываются явным образом. Вообще,
//неплохо иметь такой оператор-ловушку на случай, если в процессе разработки понадобится
//добавить в код новые исключения.
//Преимущество этих уточненных операторов catch в том, что они позволяют применить
//к разным ошибкам различные механизмы восстановления или неудачного
//завершения. Например. вы можете прекратить выполнение программы, записать в
//журнал информацию об ошибке и продолжить выполнение или повторно сгенерировать
//исключение.

//Еще один вариант, которым можно воспользоваться, - сгенерировать новое исключение,
//которое будет перекрывать текущее. Это позволяет привлечь внимание
//к ошибке, добавить собственную контекстную информацию и в то же время сохранить
//данные, зафиксированные в исключении, которое обработала ваша программа.

//Итак, генерируя исключение, вы заставляете клиентский код брать на себя ответственность
//за его обработку. Но это не отказ от ответственности. Исключение
//должно генерироваться, когда метод обнаруживает ошибку, но не имеет контекстной
//информации, чтобы правильно ее обработать. Метод write() в нашем примере
//знает, когда попытка сделать запись заканчивается неудачей и почему. но не знает,
//что с этим делать. Именно так и должно быть. Если бы мы сделали класс Conf более
//сведущим, чем он есть в настоящее время, он бы потерял свою универсальность и
//перестал бы быть повторно используемым.