<?php
abstract class Command {
    abstract function execute(CommandContext $context);
}

class LoginCommand extends Command {
    function execute(CommandContext $context) {
        $manager = Registry::getAccessManager();
        $user = $context->get('username');
        $pass = $context->get('pass');

        $user_obj = $manager->login($user,$pass);
        if(is_null($user_obj)) {
            $context->setError($manager->getError());
            return false;
        }

        $context->addParam("user", $user_obj);
        return true;
    }
}

class FeedbackCommand extends Command {
    function execute(CommandContext $context) {
        $msgSystem = Registry::getMessageSystem();
        $email = $context->get('email');
        $msg = $context->get('msg');
        $topic = $context->get('topic');
        $result = $msgSystem->send($email,$msg,$topic);
        if(!$result) {
            $context->setError($msgSystem->getError());
            return false;
        }
        return true;
    }
}

class CommandContext {
    private $params = [];
    private $error = '';

    function __construct() {
        $this->params = $_REQUEST;
    }

    function addParams($key,$val) {
        $this->params[$key] = $val;
    }

    function get($key) {
        if(isset($this->params[$key])) {
            return $this->params[$key];
        }
        return null;
    }

    function setError($error) {
        $this->error = $error;
    }

    function getError() {
        return $this->error;
    }
}

class CommandNotFoundException extends Exception {}

class CommandFactory {
    private static $dir = 'commands';

    static function getCommand($action = 'Default') {
        if(preg_match('/\w/',$action)) {
            throw new Exception("Недопустимые символы в команде");
        }

        $class = ucfirst(strtolower($action)) . "Command";
        $file = self::$dir . DIRECTORY_SEPARATOR . "{$class}.php";
        if(!file_exists($file)) {
            throw new CommandNotFoundException("Файл '$file' не найден");
        }

        require_once($file);
        if(!class_exists($class)) {
            throw new CommandNotFoundException("Класс '' не обнаружен");
        }
        $cmd = new $class();
        return $cmd;
    }
}

class Controller {
    private $context;

    function __construct() {
        $this->context = new CommandContext();
    }

    function getContext() {
        return $this->context;
    }

    function process() {
        $action = $this->context->get('action');
        $action = (is_null($action)) ? "default" : $action;
        $cmd = CommandFactory::getCommand($action);
        if(!$cmd->execute($this->context)) {
            // Обработка ошибки
        } else {
            // Все прошло успешно
            // Теперь отобразим результаты
        }
    }
}
//-------------------------------- RUN
$controller = new Controller();
//Эмулируем запрос пользователя
$context = $controller->getContext();
$context->addParam('action','login');
$context->addParam('username','bob');
$context->addParam('pass','tiddles');
$controller->process();
//-------------------------------- DOCUMENTATION
//Во всех системах должно приниматься решение о том, что делать в ответ на запрос
//пользователя. В РНР процесс принятия решения часто осуществляется с помощью
//ряда отдельных контактных страниц. Выбирая страницу (feedback.php). пользователь
//явно дает понять функциям и интерфейсу, что ему требуется. Все чаще
//программисты на РНР делают выбор в пользу "единственной точки контакта". Но в
//любом случае получатель запроса должен передать полномочия уровню, более
//связанному с логикой приложения. Такое делегирование особенно важно, если
//пользователь может сделать запросы на разные страницы. В противном случае
//дублирование кода в проекте неизбежно. Итак, предположим , что у нас есть проект
//с рядом задач, которые нужно вьшолнить. В частности , наша система должна разрешать
//одним пользователям входить в систему, а другим - оставлять отклики. Мы можем создать
//страницы login.php и feedback.php, которые решают эти задачи, создавая экземпляры
//соответствующих специализированных классов, которые и выполнят нужную работу. К сожалению,
//пользовательский интерфейс в системе редко точно соответствует задачам, для
//решения которых предназначена система. Например, функции входа в систему и
//оставления откликов могут понадобиться нам на каждой странице. Если страницы
//должны решать много различных задач, то, вероятно, мы должны представлять
//себе задачи как нечто, что можно инкапсулировать. Таким способом мы упростим
//добавление новых задач к системе и построим границу между уровнями системы.
//И это, конечно, приведет нас к шаблону Command.

//Класс LoginCommand предназначен для работы с объектом типа AccessManager.
//AccessManager - это воображаемый класс, задача которого - управлять механизмом
//входа пользователей в систему. Обратите внимание на то, что нашему методу
//Command::execute() требуется передать объект CommandContext(в книге Core J2EE
//Patterns он называется RequestHelper). Это механизм, посредством которого данные
//запроса могут быть переданы объектам Command, а ответы - отправлены назад
//на уровень представления. Использовать объект таким способом полезно, потому
//что мы можем передать различные параметры командам, не нарушая интерфейс.
//Класс CommandContext - это, по сути, объект-оболочка для ассоциативного массива
//переменных, хотя его часто расширяют для выполнения дополнительных полезных
//задач.

//Класс CommandFactory просто ищет в каталоге commands определенный файл класса.
//Это имя файла конструируется с помощью параметра $action объекта CommaпdCoпtext,
//который, в свою очередь, был передан системе из запроса. Если файл найден
//и класс существует, то он возвращается вызывающему объекту. Сюда можно добавить
//еще больше операций проверки ошибок, чтобы убедиться, что найденный
//класс принадлежит семейству Coпunand и что конструктор не ожидает аргументов, но
//данный вариант полностью подходит для наших целей. Преимущество данного подхода
//в том, что вы можете добавить новый объект Command в каталог команд в любое
//время, и система сразу станет поддерживать его.
//Вызывающий объект теперь - сама простота.

//Прежде чем вызвать метод Controller::process(). мы имитируем веб-запрос,
//определяя параметры объекта CommandContext, экземплтяр которого создан в конструкторе
//контроллера. В методе process() запрашивается значение параметра 'action' и, если его
//не существует, используется строка 'default'. Затем метод process() делегирует создание
//экземплтяров объектов объекту CommandFactory, после чего он вызывает метод execute()
//для возвращенного командного объекта. Обратите внимание на то, что контроллер не имеет
//представления о внутреннем содержании команды. Именно эта независимость от деталей
//выполнения команды дает нам возможность добавлять новые классы Command без воздействия
//на всю систему в целом.