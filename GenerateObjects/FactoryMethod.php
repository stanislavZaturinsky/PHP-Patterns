<?php
abstract class ApptEncoder {
    abstract function encode();
}

class BloggsApptEncoder extends ApptEncoder {
    function encode() {
        return "Данные о встрече закодированы в формате BloggsCal<br>";
    }
}

abstract class CommsManager {
    abstract function getHeaderText();
    abstract function getApptEncoder();
    abstract function getFooterText();
}

class BloggsCommsManager extends CommsManager {
    function getHeaderText() {
        return "BloggsCal верхний коллонтитул<br>";
    }

    function getApptEncoder() {
        return new BloggsApptEncoder();
    }

    function getFooterText() {
        return "BloggsCal нижний колонтитул<br>";
    }
}
//---------------------------------------------- RUN
$mgr = new BloggsCommsManager();
print $mgr->getHeaderText();
print $mgr->getApptEncoder()->encode();
print $mgr->getFooterText();
//---------------------------------------------- RESULT
//BloggsCal верхний коллонтитул
//Данные о встрече закодированы в формате BloggsCal
//BloggsCal нижний колонтитул

//------------------------------------------ DOCUMENTATION
//Factory Method относиться к классу порождающих паттернов. Они используются для определения и поддержания отношений между объектами.
//Фабричные методы избавляют проектировщика от необходимости встраивать в код зависящие от приложения классы.

//При разработке приложения далеко не всегда можно заранее решить, какие именно компоненты понадобятся. Обычно есть лишь общее видение того,
//что должны делать компоненты, но реализация функциональности компонентов с уточнением их возможностей выполняется позже, в ходе работы над
//проектом. Данную проблему можно решить, используя интерфейсы. Но из интерфейса невозможно создать объект. В такой ситуации необходимые
//объекты можно создавать с помощью шаблона Factory Method.