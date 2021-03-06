<?php
//-------------------------------------------------------------- Типы данных
//------------------------------- Основные существующие типы данных.
//Скалярные типы данных:
//  Двоичные данные (boolean)
//  Целые числа (Integer)
//  Числа с плавающей точкой (Float)
//  Строки (String)
//Смешанные типы данных
//  Массивы (Array)
//  Объекты (Object)
//Специальные типы данных
//  Ресурсы (Resource)
//  Пустой тип (NULL)
//Псевдотипы данных
//  Числа (Number)

//------------------------------- Приведение типов.
//(int), (integer) - приведение к integer
//(bool), (boolean) - приведение к boolean
//(float), (double), (real) - приведение к float
//(string) - приведение к string
//(array) - приведение к array
//(object) - приведение к object
//(unset) - приведение к NULL (PHP 5)

//------------------------------- Проверка типа на соответствие
//interface MyInterface
//{
//}
//
//class MyClass implements MyInterface
//{
//}
//
//$a = new MyClass;
//
//var_dump($a instanceof MyClass);
//var_dump($a instanceof MyInterface);

//====================================================================================
//-------------------------------------------------------------- Классы
//====================================================================================

//------------------------------- Виды классов
//Классы образуют четко определенную иерархию. Каждый класс, находящийся на нижних уровнях
//иерархии, обладает более четкой специализацией, чем расположенные выше классы.
//Специализированные классы называются производными классами, а тот класс, который они
//«уточняют», называется родительским классом.

//------------------------------- Геттеры, сеттеры, конструктор, деструктор
//class Animal {
//    function __get($property) {
//        //...
//    }
//
//    function __set($property, $value) {
//        //...
//    }
//}
//
//$cow = new Animal;
//$cow->weight = '1 ton'; // same as $cow->__set('weight', '1 ton')
//print $cow->weight;     // same as print $cow->__get('weight');

//Как правило, вышеперечисленные методы используются для создания динамических свойств.
//Какой вывод можно из этого сделать? Если вы хотите создавать любые случайные свойства,
//просто используйте хэш (он же массив с ключами).

//Что же хорошего в getter’ах и setter’ах?
//class Animal
//{
//    public $weightInKgs;
//}
//
//$cow = new Animal;
//$cow->weightInKgs = -100;

//Что? Вес с отрицательным значением? Это с большинства точек зрения неправильно.
//Корова не должна весить меньше 100 кг (я так думаю :). В пределах 1000 — допустимо.
//Как же нам обеспечить такое ограничение.
//Использовать __get и __set — довольно быстрый способ.

//class Animal
//{
//    private $properties = array();
//
//    public function __get($name) {
//        if(!empty($this->properties[$name])) {
//            return $this->properties[$name];
//        } else {
//            throw new Exception('Undefined property '.$name.' referenced.');
//        }
//    }
//
//    public function __set($name, $value) {
//        if($name == 'weight') {
//            if($value < 100) {
//                throw new Exception("The weight is too small!")
//      }
//        }
//        $this->properties[$name] = $value;
//    }
//}
//
//$cow = new Animal;
//$cow->weightInKgs = -100; // throws an Exception

//А что если у вас есть класс с 10—20 свойствами и проверками для них? В этом случае неприятности
//неизбежны.
//public function __set($name, $value) {
//    if($name == 'weight') {
//        if($value < 100) {
//            throw new Exception("The weight is too small!")
//      }
//        if($this->weight != $weight) {
//            Shepherd::notifyOfWeightChange($cow, $weight);
//        }
//    }
//
//    if($name == 'legs') {
//        if($value != 4) {
//            throw new Exception("The number of legs is too little or too big")
//      }
//        $this->numberOfLegs = $numberOfLegs;
//        $this->numberOfHooves = $numberOfLegs;
//    }
//
//    if($name == 'milkType') {
//        .... you get the idea ....
//    }
//    $this->properties[$name] = $value;
//}

//И наоборот, getter’ы и setter’ы проявляют себя с лучшей стороны, когда дело доходит до проверки данных.
//class Animal
//{
//    private $weight;
//    private $numberOfLegs;
//    private $numberOfHooves;
//    public $nickname;
//
//
//    public function setNumberOfLegs($numberOfLegs)
//    {
//        if ($numberOfLegs != 100) {
//            throw new Exception("The number of legs is too little or too big");
//        }
//        $this->numberOfLegs = $numberOfLegs;
//        $this->numberOfHooves = $numberOfLegs;
//    }
//
//    public function getNumberOfLegs()
//    {
//        return $this->numberOfLegs;
//    }
//
//
//    public function setWeight($weight)
//    {
//        if ($weight < 100) {
//            throw new Exception("The weight is too small!");
//        }
//        if($this->weight != $weight) {
//            Shepherd::notifyOfWeightChange($cow, $weight);
//        }
//        $this->weight = $weight;
//    }
//
//    public function getWeight()
//    {
//        return $this->weight;
//    }
//}

//====================================================================================
//-------------------------------------------------------------- Обработка ошибок и исключений
//====================================================================================

//Эта функция используется для определения собственного обработчика ошибок времени выполнения скрипта.
//Например, если требуется очистить данные/файлы, когда произошла критическая ошибка, или если нужно
//переключить тип ошибки, исходя из каких-то условий (используя функцию trigger_error()).

//функция обработки ошибок
//function myErrorHandler($errno, $errstr, $errfile, $errline)
//{
//    if (!(error_reporting() & $errno)) {
//        // Этот код ошибки не включен в error_reporting
//        return;
//    }
//
//    switch ($errno) {
//        case E_USER_ERROR:
//            echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
//            echo "  Фатальная ошибка в строке $errline файла $errfile";
//            echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
//            echo "Завершение работы...<br />\n";
//            exit(1);
//            break;
//
//        case E_USER_WARNING:
//            echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
//            break;
//
//        case E_USER_NOTICE:
//            echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
//            break;
//
//        default:
//            echo "Неизвестная ошибка: [$errno] $errstr<br />\n";
//            break;
//    }
//
//    /* Не запускаем внутренний обработчик ошибок PHP */
//    return true;
//}

// функция для тестирования обработчика ошибок
//function scale_by_log($vect, $scale)
//{
//    if (!is_numeric($scale) || $scale <= 0) {
//        trigger_error("log(x) для x <= 0 не определен, вы используете: scale = $scale", E_USER_ERROR);
//    }
//
//    if (!is_array($vect)) {
//        trigger_error("Некорректный входной вектор, пропущен массив значений", E_USER_WARNING);
//        return null;
//    }
//
//    $temp = array();
//    foreach($vect as $pos => $value) {
//        if (!is_numeric($value)) {
//            trigger_error("Значение на позиции $pos не является числом, будет использован 0 (ноль)", E_USER_NOTICE);
//            $value = 0;
//        }
//        $temp[$pos] = log($scale) * $value;
//    }
//
//    return $temp;
//}

//// переключаемся на пользовательский обработчик
//$old_error_handler = set_error_handler("myErrorHandler");
//
//// вызовем несколько ошибок, во-первых, определим массив с нечисловым элементом
//echo "vector a\n";
//$a = array(2, 3, "foo", 5.5, 43.3, 21.11);
//print_r($a);
//
//// теперь создадим еще один массив
//echo "----\nvector b - a notice (b = log(PI) * a)\n";
///* Значение на позиции $pos не является числом, будет использован 0 (ноль)*/
//$b = scale_by_log($a, M_PI);
//print_r($b);
//
//// проблема, мы передаем строку вместо массива
//echo "----\nvector c - a warning\n";
///* Некорректный входной вектор, пропущен массив значений */
//$c = scale_by_log("not array", 2.3);
//var_dump($c); // NULL
//
//// критическая ошибка, логарифм от неположительного числа не определен
//echo "----\nvector d - fatal error\n";
///* log(x) для x <= 0 не определен, вы используете: scale = $scale */
//$d = scale_by_log($a, -2.5);
//var_dump($d); // До сюда не доберемся никогда

//если метод обработки ошибок находится в классе
//set_error_handler(array('Class', 'method'));
//// since PHP 5.2.3
//set_error_handler('Class::method');

//====================================================================================
//-------------------------------------------------------------- Валидация и фильтрация пользовательских данных.
//                                                               Регулярные выражения.
//====================================================================================

//------------------------------- Валидация и фильтрация пользовательских данных.
//$name = $_POST['name'];
//$surname = $_POST['surname'];
//$email = $_POST['email'];
//$message = $_POST['message'];

//функция для очистки данных от HTML и PHP тегов:
//function clean($value = "") {
//    $value = trim($value);
//    $value = stripslashes($value);
//    $value = strip_tags($value);
//    $value = htmlspecialchars($value);
//
//    return $value;
//}

//Здесь, мы использовали функцию trim для удаления пробелов из начала и конца строки.
//Функция stripslashes нужна для удаления экранированных символов
//("Ваc зовут O\'reilly?" => "Вас зовут O'reilly?").
//Функция strip_tags нужна для удаления HTML и PHP тегов. Последня функция - htmlspecialchars
//преобразует специальные символы в HTML-сущности ('&' преобразуется в '&amp;' и т.д.)

//Result:
//$name = clean($name);
//$surname = clean($surname);
//$email = clean($email);
//$message = clean($message);
//
//if(!empty($name) && !empty($surname) && !empty($email) && !empty($message)) {
//    ...
//}

//Примеры фильтраций:
//Если вы используете чекбоксы или мультиселекты с числовыми значениями, выполните такую проверку:
//$checkbox_arr = array_map('intval', $_POST['checkbox']);

//Если вы не предполагаете вхождение html тегов, то лучше всего сделать такую фильтрацию:
//$input_text = strip_tags($_GET['input_text']);
//$input_text = htmlspecialchars($input_text);
//$input_text = mysql_escape_string($input_text);

//strip_tags — убирает html теги.
//htmlspecialchars — преобразует спец. символы в html сущности.
//Так вы защитите себя от XSS атаки, помимо SQL инъекции.

//Если же вам нужны html теги, но только как для вывода исходного кода, то достаточно использовать:
//$input_text = htmlspecialchars($_GET['input_text']);
//$input_text = mysql_escape_string($input_text);

//Если вам важно, чтобы значение переменной не было пустой, то используйте функцию trim, пример:
//$input_text = trim($_GET['input_text']);
//$input_text = htmlspecialchars($input_text);
//$input_text = mysql_escape_string($input_text);

//------------------------------- Регулярные выражения.
//Регулярные выражения — мощный и филигранный инструмент для поиска по шаблону и обработки текста.
//Уступая по скорости простым операциям поиска в строках, регулярные выражения отличаются исключительной гибкостью.
//Регулярные выражения - мощный гибкий инструмент для синтаксического анализа текста в соответствии с
//определенным шаблоном.
//
//Шаблон - строка символов, спецсимволов и модификаторов, описывающих правила, которым должен
//соответствовать разбираемый текст.

//1 - Спецсимволы.
//Сначала о них, потому что мне будет легче описать остальное.
//\ - символ экранирования.
//Пример: '/qwe\/rty/' - соответствует строке, в которой есть qwe/try. Символ / мы заэкранировали, после чего
//он перестал выполнять в данном месте свое специальное значение (он являлся ограничителем шаблона).
//^ - символ начала данных.
//$ - символ конца данных.
//Пример: '/^pattern$/' - Соответствует строке, точно совпадающей с словом pattern. Т.е. с буквы p строка
//начинается и после n заканчивается.
//. - любой символ, кроме перевода строки. Но есть модификатор, при использовании которого перевод строки
//тоже относится к "любым" символам.
//Пример: '/pat.ern/' - Соответствует и строке, содержащей pattern, или patdern, или pat3ern...
//[] - внутри этих скобок перечисляются символы, любой один символ из которых может стоять на данном месте.
//Это называется символьным классом. Спецсимволы, написанные в [] ведут себя немного по-другому. Это я напишу.
//Пример: '/pat[aoe]rn/' - под соответствие попадут только строки, содержащие patarn, patorn или patern.
//| - Или. Пример ниже.
//    () - подмаска.
//? - одно или ноль вхождений предшествующего символа или подмаски.
//* - любое количество вхождений предшествующего символа или подмаски. В том числе и ноль.
//+ - одно или более вхождений.
//Пример: '/as+(es|du)?.*r/' - Буква а, потом одна или больше букв s, после этого сочетание es или du может быть
//один раз, а может м ни разу, потом любое количество любых символов и буква r.
//Здесь же скажу про еще одно значения символа ?. Метасимвол звездочка по умолчанию жадный (и другие тоже).
//Это значит, что в нашем примере вот этой части '.*r' будет соответствовать, например, подстрока asdrfsrsfdr.
//Как видно, до последней буквы r в нее попало еще две. Вот эту жадность можно выключить. Т.е. шаблон станет
//соответствовать только подстроке asdr. До первого r. Для этого надо в до того места где необходимо отключить
//жадность поставит модификатор (?U). Вот еще одно применение символам ? и ().
//{a,b} - количество вхождений предшествующего символа или подмаски от а до б. Если б не указан, считается, что
//верхней границы нет. Например, * - то же самое, что {0,}. ? - то же, что {0,1}. {5,7} - 5,6 или 7 повторений.

//a) Спецсимволы внутри символьного класса.
//^ - отрицание.
//Пример: [^da] - соответствует любому символу кроме d и a.
//Пример: [^^] - соответствует любому символу кроме ^.
//Пример: [d^a] - соответствует любому символу из перечисленных трёх. [\^da] - то же самое.
//В последнем примере, как видно символ стоит не в начале перечисления и свою метафункцию теряет. И экранировать
//его, кстати, тоже тут не надо.
//- - внутри символьного класса означает символьный интервал.
//Пример: [0-9a-e] - соответствует любому символу от 0 до 9 и от a до e. Если в символьном классе надо перечислить
//сам символ дефиса, то следует либо заэранироватьего, либо разместить перед ].
//Осторожно в символьном классе надо использовать символ \. Если его поставить перед ], она окажется заэкранирована.
//Также окажется заэкранированным любой символ, который может быть заэкранирован. Иначе символ \ является обычным
//символом.
//Символ $ тоже является обычным символом внутри символьного класса. И скобки тоже.
//
//б) Символ \. Одна из его функций - снятие специального значения с спецсимволов. А другая, наоборот придание
//специальных функций обычным символам.
//\cx - ctrl + x. На месте x может быть любой символ.
//\e - escape.
//\f - разрыв страницы.
//\n, \r, \t - это нам и так привычно. Перевод строки, возврат каретки и табуляция.
//\d - любой символ, означающий десятичную цифру.
//\D - любой символ, не означающий десятичную цифру.
//\s - любой пробельный символ.
//\S - не пробельный.
//\w - любая цифра, буква или знак подчеркивания.
//\W - любой символ, но не \w.
//\b - граница слова. Можно использовать вместо \w\W или \W\w или ^\w или \w$
//\B - не граница слова.
//Две последние конструкции не соответствуют никаким реальным символам.
//\xHH - символ с шестнадцатиричным кодом HH. x - это именно буква икс.
//\DDD - символ с восьмиричным кодом DDD. Или ссылка на подмаску.
//
//По поводу ссылки на подмаску: '/([0-9]{2,3}).\1/' - 2 или 3 символа от 0 до 9, потом любая последовательность символов
//и те же 2 или 3 конкретных символа, которые соответствовали подмаске. То есть строка 'as34sdf34' - подойдет. Там 34,
//и там. А 'sd34dg32' - нет.
//Если анализатор находит \x, он считывает максимальное количество последующих символов, которые могут быть
//шестнадцатиричным числом. Максимальное - это не больше двух. Если из три, то считается два, если меньше - то сколько
//есть.
//Если анализатор находит \0, он поступает аналогично. Только считывает не 16ричные, а восмеричные цифры. До двух штук.
//То есть \0\x\0325 означает два символа с кодом ноль, символ с восьмеричным кодом 32 и пятерка.
//Если после слеша стоит отличная от нуля цифра, то ту посложнее. Вот напишем такую вещь: \40. Если в шаблоне есть 40
//подмасок, то это будет воспринято как ссылка на 40ю подмаску. Сороковую - в десятичной системе счисления. Если же
//подмасок меньше, то это будет воспринято как символ с восьмеричном кодом 40.
//\040 - всегда символ с кодом и восьмеричным 40.
//\7 - всегда ссылка на подмаску.
//\13 - в зависимости от ситуации.
//В символьном классе возможно указывать символьные диапазоны с помощью из кодов: [\044-\056]
//Стоит также отметить, что ссылок на подмаски не может быть больше, чем 99.

//2 - Обычные символы.
//Это символы, не являющиеся специальными.

//3 - Модификаторы.
//Указываются они либо в скобках, например так: (?Ui), либо после закрывающего символа '/pattern/Ui'.
//i - регистронезависимость.
//U - инвертирует жадность.
//m - многострочный поиск.
//s - если используется, то символ . соответствует и переводу строки. Иначе она ему не соответствует.
//x - заставляет игнорировать все неэкранированные пробельные символы, если они не перечислены в символьном классе.
//Удобно, когда энтерами и пробелами вы хотите навести удобночитаемость в регулярке.
//При использовании модификаторов, можно использовать знак '-' для отключения модификатора. (?m-i) - Bключаем
//многострочный поиск и отключаем регистронезависимый.
//Здесь надо сказать, что все модификаторы что-то включают. Или отключают, если указаны с минусом. А вот U
//инвертирует. Т.е. если была жадность включена, он выключит без всяких минусов.

//4 - Утверждения.
//Утверждения - это проверки касательно символов, идущих до или после текущей позиции сопоставления. Например,
//\b - это утверждение, что предыдущий символ словесный, а следующий - нет, либо наоборот. Но это как бы встроенное
//утверждение, а мы тут сейчас свои собственные научимся писать.
//Утверждения касательно последующего текста начинаются с (?= для положительных утверждений и с (?! для отрицающих
//утверждений. Утверждения касательно предшествующего текста начинаются с (?<= для положительных утверждений и (?<!
//для отрицающих. Например, '/(?<!foo)bar/' не найдёт вхождения "bar", которым не предшествует "foo". Т.е. qwefoobar
//этот шаблон проигнорирует, а asacdbar под него подойдет.
//(?<=\d{3})(?<!999)foo совпадает с подстрокой "foo", которой предшествуют три цифры, отличные от "999". Следует
//понимать, что каждое из утверждений проверяется относительно одной и той же позиции в обрабатываемом тексте.
//Утверждения могут быть вложенными, причем в произвольных сочетаниях: (?<=(?<!foo)bar)baz соответствует подстроке
//"baz", которой предшествует "bar", перед которой, в свою очередь, нет 'foo'.

//a) Условные подмаски.
//По-моему, этого достаточно: (?(condition)yes-pattern|no-pattern)
//Пример: (?(?=\d)u|p). (?=\d) - это условие. Мы утверждаем, что после этого места идет цифра. Если оно истинно, то на
//данном месте должна стоять буква u. Иначе - p.

//5 - Комментарии.
//Комментарии начинаются с (?# и продолжаются до ближайшей закрывающей скобки. Так же как /* */ в PHP - без учета
//вложенности.

//====================================================================================
//-------------------------------------------------------------- Суперглобальные массивы. Их назначение
//====================================================================================

//$GLOBALS — Ассоциативный массив (array), содержащий ссылки на все переменные глобальной области видимости
//скрипта, определенные в данный момент. Имена переменных являются ключами массива.

//$_SERVER - это массив, содержащий информацию, такую как заголовки, пути и местоположения скриптов.
//Записи в этом массиве создаются веб-сервером. Нет гарантии, что каждый веб-сервер предоставит любую
//из них; сервер может опустить некоторые из них или предоставить другие, не указанные здесь.

//$_GET - Ассоциативный массив параметров, переданных скрипту через URL.

//$_POST - Ассоциативный массив данных, переданных скрипту через HTTP метод POST.

//$_FILES - Ассоциативный массив (array) элементов, загруженных в текущий скрипт через метод HTTP POST.

//$_COOKIE - Ассоциативный массив (array) значений, переданных скрипту через HTTP Куки.

//$_SESSION - Ассоциативный массив, содержащий переменные сессии, которые доступны для текущего скрипта.

//$_REQUEST — Переменные HTTP-запроса

//$_ENV - Переменные окружения

//====================================================================================
//-------------------------------------------------------------- Работа с файлами и папками. Генерация, удаление,
//                                                               запись, обновление, права.
//====================================================================================

//------------------------------- Работа с файлами
//$fp = fopen('counter.txt', 'r');

//Согласно документации PHP выделяют следующие виды режимов файлов:
//r  – открытие файла только для чтения.
//r+ - открытие файла одновременно на чтение и запись.
//w  – создание нового пустого файла. Если на момент вызова уже существует такой файл, то он уничтожается.
//w+ - аналогичен r+, только если на момент вызова фай такой существует, его содержимое удаляется.
//a  – открывает существующий файл в  режиме записи, при этом указатель сдвигается на  последний байт файла (на конец
//файла).
//a+ - открывает файл в режиме чтения и записи при этом указатель сдвигается на последний байт файла (на конец файла).
//Содержимое файла не удаляется.

//fwrite($fp, $mytext); // Запись в файл

//Для построчного считывания файла используют функцию fgets()
//$fp = fopen("counter.txt", "r"); // Открываем файл в режиме чтения
//if ($fp)
//{
//    while (!feof($fp)) // feof - Проверяет, достигнут ли конец файла
//    {
//        $mytext = fgets($fp, 999);
//        echo $mytext."<br />";
//    }
//}
//else echo "Ошибка при открытии файла";
//fclose($fp);

//echoreadfile("counter.txt"); - считываем весь файл

//$file_array = file("counter.txt"); // Считывание файла в массив $file_array

//====================================================================================
//-------------------------------------------------------------- Автоматическая загрузка классов
//====================================================================================

//Большинство разработчиков объектно-ориентированных приложений используют такое соглашение именования файлов,
//в котором каждый класс хранится в отдельно созданном для него файле. Одной из наиболее при этом досаждающих
//деталей является необходимость писать в начале каждого скрипта длинный список подгружаемых файлов.

//В PHP 5 это делать не обязательно. Можно определить функцию __autoload(), которая будет автоматически вызвана
//при использовании ранее неопределенного класса или интерфейса. Вызов этой функции - последний шанс для
//интерпретатора загрузить класс прежде, чем он закончит выполнение скрипта с ошибкой.

//Example 1
//function __autoload($class_name) {
//    include $class_name . '.php';
//}
//
//$obj  = new MyClass1();
//$obj2 = new MyClass2();

//Example 2
//function __autoload($name) {
//    echo "Want to load $name.\n";
//    throw new Exception("Unable to load $name.");
//}
//
//try {
//    $obj = new NonLoadableClass();
//} catch (Exception $e) {
//    echo $e->getMessage(), "\n";
//}

//====================================================================================
//-------------------------------------------------------------- Работа с сессиями и куками. Понимание связи между ними.
//====================================================================================

//Сессии и cookies предназначены для хранения сведений о пользователях при переходах между несколькими
//страницами. При использовании сессий данные сохраняются во временных файлах на сервере. Файлы с cookies
//хранятся на компьютере пользователя, и по запросу отсылаются броузером серверу.
//Использование сессий и cookies очень удобно и оправдано в таких приложениях как Интернет-магазины, форумы,
//доски объявлений, когда, во-первых, необходимо сохранять информацию о пользователях на протяжении нескольких
//станиц, а, во-вторых, своевременно предоставлять пользователю новую информацию.

//Протокол HTTP является протоколом "без сохранения состояния". Это означает, что данный протокол не имеет
//встроенного способа сохранения состояния между двумя транзакциями. Т. е., когда пользователь открывает
//сначала одну страницу сайта, а затем переходит на другую страницу этого же сайта, то основываясь только
//на средствах, предоставляемых протоколом HTTP невозможно установить, что оба запроса относятся к одному
//пользователю. Т. о. необходим метод, при помощи которого можно было бы отслеживать информацию о
//пользователе в течение одного сеанса связи с Web-сайтов. Одним из таких методов является управление
//сеансами при помощи предназначенных для этого функций. Для нас важно то, что сеанс по сути, представляет
//собой группу переменных, которые, в отличие от обычных переменных, сохраняются и после завершения
//выполнения PHP-сценария.

//------------------------------- Куки
//Использование сookies удобно как для программистов, так и для пользователей. Пользователи выигрывают
//за счет того, что им не приходится каждый раз заново вводить информацию о себе, а программистам
//сookies помогают легко и надежно сохранять информацию о пользователях.

//Определение Cookies - это текстовые строки, хранящиеся на стороне клиента, и содержащие пары "имя-значение",
//с которыми связан URL, по которому броузер определяет нужно ли посылать cookies на сервер.

//Установка cookies производится с помощью функции setcookie("counter",$counter);

//Скрипт для проверки работоспособности cookie в браузере.

//if (empty($_GET["cookie"])){
//    // пробуем отправить cookies в браузер
//    header("Location: $_SERVER[PHP_SELF]?cookie=1");
//    setcookie("test","1");
//} else {
//    if (empty($_COOKIE["test"])){
//        echo("Включите cookies в браузере!");
//    } else { // всё впоряде, перенаправляем на нужную страницу
//        header("Location: http://localhost/");
//    }
//}

//====================================================================================
//----------------------- Работа с Http заголовками. ПРавильный ответ сервера на те или инные
//                        ситуации (OK, ошибка, доступ запрещен, редирект и тп).
//====================================================================================

