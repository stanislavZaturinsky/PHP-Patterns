<?php
//248
abstract class Expression {
    private static $keycount = 0;
    private $key;

    abstract function interpret( InterpreterContext $context );

    function getKey() {
        if ( !isset( $this->key ) ) {
            self::$keycount++;
            $this->key = self::$keycount;
        }
        return $this->key;
    }
}

class LiteralExpression extends Expression {
    private $value;

    function __construct ( $value ) {
        $this->value = $value;
    }

    function interpret( InterpreterContext $context ) {
        $context->replace( $this, $this->value );
    }
}

class VariableExpression extends Expression {
    private $name;
    private $val;

    function __construct( $name, $val = null) {
        $this->name = $name;
        $this->val = $val;
    }

    function interpret( InterpreterContext $context ) {
        if ( !is_null($this->val)) {
            $context->replace($this, $this->val);
            $this->val = null;
        }
    }

    function setValue( $value ) {
        $this->val = $value;
    }

    function getKey() {
        return $this->name;
    }
}

class InterpreterContext {
    private $expressionstore = [];

    function replace( Expression $exp, $value ) {
        $this->expressionstore[$exp->getKey()] = $value;
    }

    function lookup( Expression $exp ) {
        return $this->expressionstore[$exp->getKey()];
    }
}
//-------------------------------- RUN
$context = new InterpreterContext;
$myvar = new VariableExpression( 'input', 'Четыре' );
$myvar->interpret( $context );
print $context->lookup( $myvar ) . "<br>";

$newvar = new VariableExpression( 'input' );
$newvar->interpret( $context );
print $context->lookup( $newvar ) . "<br>";

$myvar->setValue( 'Пять' );
$myvar->interpret( $context );
print $context->lookup( $myvar ) . "<br>";
print $context->lookup( $newvar ) . "<br>";
//-------------------------------- RESULT
//Четыре
//Четыре
//Пять
//Пять

//-------------------------------- DOCUMENTATION
//Конструктору класса VariableExpression передаются два аргумента (имя и значение),
//которые сохраняются в свойствах объекта. В классе реализован метод setValue(),
//чтобы клиентский код мог изменить значение переменной в любое время.
//Метод interpret{) проверяет, имеет ли свойство $val ненулевое значение. Если
//у свойства $val есть некоторое значение, то его значение сохраняется в объекте
//InterpreterContext. Затем мы устанавливаем для свойства $val значение null. Это
//делается для того, чтобы повторный вызов метода interpret( ) не испортил значение
//переменной с тем же именем, сохраненной в объекте InterpreterContext другим
//экземпляром объекта VariableExpression . Возможности нашей переменной
//достаточно ограничены, так как ей могут быть присвоены только строковые значения.
//Если бы мы собирались расширить наш язык, то нужно было бы сделать так,
//чтобы он работал с другими объектами типа Expression, содержащими результаты
//выполнения булевых и других операций. Но пока VariableExpression будет делать
//то. что нам от него нужно. Обратите внимание на то, что мы заменили метод
//getKey(), чтобы значения переменных были связаны с именем переменной. а не с
//произвольным статическим идентификатором.

//Проблемы шаблона lnterpreter
//Как только вы подготовите основные классы для реализации шаблона Interpreter.
//расширить его будет легко. Цена, которую за это приходится платить. - только количество
//классов, которые нужно создать. Поэтому шаблон Interpreter более применим
//для относительно небольших языков. А если вам нужен полноценный язык программирования,
//то лучше поискать для этой цели инструмент от сторонних фирм.
//Поскольку классы Interpreter часто выполняют очень схожие задачи, стоит следить
//за создаваемыми классами, чтобы не допускать дублирования. Многие люди.
//которые в первый раз обращаются к шаблону Interpreter, после некоторых начальных
//экспериментов разочаровываются, обнаружив, что он не выполняет синтаксический
//анализ. Это означает, что мы пока не в состоянии предложить пользователям
//хороший дружественный язык. В приложении Б приведен примерный вариант
//кода. иллюстрирующего одну из стратегий синтаксического анализатора миниязыка.