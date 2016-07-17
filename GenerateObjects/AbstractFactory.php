<?php
abstract class ApptEncoder {
    abstract function encode();
}

abstract class TtdEncoder {
    abstract function encode();
}

abstract class ContactEncoder {
    abstract function encode();
}

class BloggsApptEncoder extends ApptEncoder {
    function encode() {
        return "Данные о встрече закодированы в формате BloggsAppt<br>";
    }
}

class BloggsContactEncoder extends ContactEncoder {
    function encode() {
        return "Данные о встрече закодированы в формате BloggsContactE<br>";
    }
}

class BloggsTtdEncoder extends TtdEncoder {
    function encode() {
        return "Данные о встрече закодированы в формате BloggsTtd<br>";
    }
}

abstract class CommsManager {
    const    APPT = 1;
    const     TTD = 2;
    const CONTACT = 3;

    abstract function getHeaderText();
    abstract function make($flag_int);
    abstract function getFooterText();
}

class BloggsCommsManager extends CommsManager {
    function getHeaderText() {
        return "BloggsComms верхний коллонтитул<br>";
    }

    function make($flag_int) {
        switch($flag_int) {
            case self::APPT:
                return new BloggsApptEncoder();
            case self::CONTACT:
                return new BloggsContactEncoder();
            case self::TTD:
                return new BloggsTtdEncoder();
        }
    }

    function getFooterText() {
        return "BloggsComms нижний колонтитул<br>";
    }
}
//---------------------------------------------- RUN
$mgr = new BloggsCommsManager();
print $mgr->getHeaderText();
print $mgr->make(CommsManager::APPT)->encode();
print $mgr->make(CommsManager::TTD)->encode();
print $mgr->make(CommsManager::CONTACT)->encode();
print $mgr->getFooterText();
//---------------------------------------------- RESULT
//BloggsComms верхний коллонтитул
//Данные о встрече закодированы в формате BloggsAppt
//Данные о встрече закодированы в формате BloggsTtd
//Данные о встрече закодированы в формате BloggsContactE
//BloggsComms нижний колонтитул

//---------------------------------------------- DOCUMENTATION
//Абстрактная фабрика (англ. Abstract factory) — порождающий шаблон проектирования, предоставляет интерфейс для создания
//семейств взаимосвязанных или взаимозависимых объектов, не специфицируя их конкретных классов. Шаблон реализуется созданием
//абстрактного класса Factory, который представляет собой интерфейс для создания компонентов системы (например, для оконного
//интерфейса он может создавать окна и кнопки). Затем пишутся классы, реализующие этот интерфейс.

//Используя шаблон Factory Method, мы определяем четкий интерфейс и заставляем все конкретные объекты фабрики подчиняться ему.
//Используя единственный метод make ( ) , мы должны помнить о поддержке всех объектов-продуктов во всех конкретных создателях.
//Мы также используем параллельные условные операторы. поскольку в каждом конкретном создателе должны быть реализованы одинаковые
//проверки флага-аргумента. Клиентский класс не может быть уверен. что конкретные создатели генерируют весь набор продуктов,
//поскольку внутренняя организация метода make ( ) может отличаться в каждом случае. С другой стороны, мы можем построить более
//гибкие создатели . В базовом классе создателя можно предусмотреть метод make ( ) , который будет гарантировать стандартную
//реализацию для каждого семейства продуктов. И тогда конкретные дочерние классы могут избирательно модифицировать это поведение.
//Реализующим создателя классам будет предоставлено право выбора - вызывать стандартный метод make ( ) после собственной
//реализации или нет.