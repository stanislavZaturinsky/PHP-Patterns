<?php
abstract class Question {
    protected $prompt;
    protected $marker;

    function __construct( $prompt, Marker $marker ) {
        $this->marker = $marker;
        $this->prompt = $prompt;
    }

    function mark( $response ) {
        return $this->marker->mark( $response );
    }
}

class TextQuestion extends Question {

}

class AVQQuestion extends Question {

}

abstract class Marker {
    protected $test;

    function __construct( $test ) {
        $this->test = $test;
    }

    abstract function mark( $response );
}

class MarkLogicMarker extends Marker {
    private $engine;

    function __construct( $test ) {
        parent::__construct( $test );
    }

    function mark( $response ) {
        return true;
    }
}

class MatchMarker extends Marker {
    function mark( $response ) {
        return ( $this->test == $response );
    }
}

class RegexpMarker extends Marker {
    function mark( $response ) {
        return (preg_match( $this->test, $response ));
    }
}
//-------------------------------- RUN
$markers = [
    new RegexpMarker( "/П.ть/"),
    new MatchMarker( "Пять" ),
    new MarkLogicMarker( '$input equals "Пять"' )
];

$variants = ["Пять", "Четыре"];
foreach( $markers as $marker ) {
    print get_class( $marker ) . "<br>";
    $question = new TextQuestion( "Сколько лучей у Кремлевской звезды?", $marker );

    foreach($variants as $response ) {
        print "\tОтвет: $response:";
        if ($question->mark( $response )) {
            print "Правильно!<br>";
        } else {
            print "Неверно!<br>";
        }
    }
}
//-------------------------------- RESULT
//RegexpMarker
//Ответ: Пять:Неверно!
//Ответ: Четыре:Неверно!
//MatchMarker
//Ответ: Пять:Правильно!
//Ответ: Четыре:Неверно!
//MarkLogicMarker
//Ответ: Пять:Правильно!
//Ответ: Четыре:Правильно!

//-------------------------------- DOCUMENTATION
//Как видите, природу различия между классами TextQuestion и AVQuestion мы не
//определяем, оставляя это на ваш суд. Все необходимые функции обеспечиваются в
//базовом классе Question ,в котором, кроме всего прочего, хранится свойство, содержашее
//вопрос и объект типа mаrker . Когда вызывается метод Question::mark() с ответом
//от конечного пользователя, этот метод просто делегирует решение проблемы
//своему объекту Marker.

//Наверное, здесь мало неожиданностей (если они есть вообще), связанных с самими
//классами Marker. Обратите внимание на то, что объект типа MarkParse предназначен
//для работы с простым синтаксическим анализатором, приведенным в приложении
//Б. Но для данного примера в этом нет необходимости, поэтому мы просто
//возвращаем значение true (истина) из метода MarkLogicMarker::mark(). Здесь главное
//- структура, которую мы определили, а не детали самих стратегий. Мы можем
//заменить RegexpMarker на MatchMarker, так что это не повлияет на класс Question.
//Разумеется, вы еще должны решить, какой метод использовать для выбора между
//конкретными объектами типа Marker. На практике я видел два подхода к решению
//этой проблемы. В первом используются переключатели для выбора предпочитаемой
//стратегии оценки. Во втором используется сама структура условия оценки.
//при этом оператор сравнения остается простым.

//Мы создали три объекта, содержащие стратегии оценки ответов, которые по
//очереди используются для создания объекта типа TextQuestion. Затем объект типа
//TextQuestion проверяется по отношению к двум вариантам ответов.
//Приведенный здесь класс MarkLogicMarker в настоящее время - это просто заглушка,
//и его метод mark() всегда возвращает значение true (истина). Однако закомментированный
//код работает с вариантом синтаксического анализатора, приведенным в приложении Б.
//Он также может работать с любым анализатором, разработанным
//сторонними производителями.

//Не забывайте, что в данном примере класс MarkLogicMarker не выполняет никаких
//действий и всегда возвращает значение true (истина), поэтому он оценил оба
//ответа как правильные.
//В данном примере мы передали данные, введенные пользователем (они содержатся
//в переменной $response), объекту-оценщику с помощью метода mark(). Но
//иногда вы будете сталкиваться с ситуациями, когда заранее не всегда известно,
//сколько информации потребует объект-оценщик при выполнении своей операции.
//Поэтому можно делегировать решения относительно того, какие данные получать,
//самому объекту-оценщику, передав ему экземпляр объекта-клиента. Затем этот
//объект-оценщик сам запросит у клиента необходимые ему данные.