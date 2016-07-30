<?php
//---------------------------------------------------------------- WITHOUT FACADE
function getProductFileLines( $file ) {
    return $file;
}

function getProductObjectFromId( $id, $productName) {
    //request to DB
    return new Product( $id, $productName);
}

function getNameFromLine( $line ) {
    if ( preg_match( "/.*-(.*)\s\d+/", $line, $array)) {
        return str_replace( '_', ' ', $array[1]);
    }
    return '';
}

function getIdFromLine( $line ) {
    if ( preg_match( "/^(\d{1,3})-/", $line, $array)) {
        return $array[1];
    }
    return -1;
}

class Product {
    public $id;
    public $name;

    function __construct( $id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
}
////-------------------------------- RUN
//$lines = getProductFileLines( 'text.txt' );
//$objects = [];
//foreach ( $lines as $line ) {
//    $id = getIdFromLine( $line );
//    $name = getNameFromLine( $line );
//    $objects[$id] = getProductObjectFromId($id, $name);
//}
//echo '<pre>';print_r($objects);die;
//---------------------------------------------------------------- WITH FACADE
class ProductFacade {
    private $products = [];

    function __construct( $file ) {
        $this->file = $file;
        $this->compile();
    }

    private function compile() {
        $lines = getProductFileLines( 'text.txt' );
        $objects = [];
        foreach ( $lines as $line ) {
            $id = getIdFromLine( $line );
            $name = getNameFromLine( $line );
            $objects[$id] = getProductObjectFromId($id, $name);
        }
    }

    function getProducts() {
        return $this->products;
    }

    function getProduct( $id ) {
        if ( isset( $this->products[$id] ) ) {
            return $this->products[$id];
        }
        return null;
    }
}
//-------------------------------- RUN
$facade = new ProductFacade( 'text.txt' );
$facade->getProduct( 234 );
//-------------------------------- DOCUMENTATION
//В основе шаблона Facade на самом деле лежит очень простая идея. Это всего
//лишь вопрос создания одной точки входа для уровня или подсистемы в целом. В
//результате мы получаем ряд преимуществ, поскольку отдельные части проекта отделяются
//одна от другой. Программистам клиентского кода полезно и удобно иметь
//доступ к простым методам. которые выполняют понятные и очевидные вещи. Это
//позволяет сократить количество ошибок, сосредоточив обращение к подсистеме в
//одном месте, так что изменения в этой подсистеме вызовут сбой в предсказуемом
//месте. Классы Facade также минимизируют ошибки в комплексных подсистемах.
//где клиентский код, в противном случае, мог бы некорректно использовать внутренние
//функции.
//Несмотря на простоту шаблона Facade, очень легко забыть воспользоваться им.
//особенно если вы знакомы с подсистемой, с которой работаете. Но, конечно, тут
//необходимо найти нужный баланс. С одной стороны, преимущества создания простых
//интерфейсов для сложных систем очевидны. С другой стороны, можно необдуманно
//разделить системы, а затем разделить разделения. Если вы осуществляете
//значительные упрощения для пользы клиентского кода и/или экранируете его от
//систем, которые могут изменяться. то. вероятно, есть основания для реализации
//шаблона Facade.