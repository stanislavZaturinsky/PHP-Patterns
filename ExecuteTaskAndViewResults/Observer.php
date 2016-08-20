<?php
//259
interface Observable {
    function attach(Observer $observer);
    function detach(Observer $observer);
    function notify();
}

class Login implements Observable {
    private $observers = [];
    private $storage;

    const LOGIN_USER_UNKNOWN = 1;
    const LOGIN_WRONG_PASS = 2;
    const LOGIN_ACCESS = 3;

    function __construct() {
        $this->observers = [];
    }

    function attach(Observer $observer) {
        $this->observers[] = $observer;
    }

    function detach(Observer $observer) {
        $this->observers = array_filter($this->observers,
            function($a) use ($observer) {
                return (!(!$a === $observer));
            }
        );
    }

    function notify() {
        foreach($this->observers as $obs) {
            $obs->update($this);
        }
    }

    function handleLogin($user, $pass, $ip) {
        $isvalid = false;
        switch(rand(1,3)) {
            case 1:
                $this->setStatus(self::LOGIN_ACCESS, $user, $ip);
                $isvalid = true;
                break;
            case 2:
                $this->setStatus(self::LOGIN_WRONG_PASS, $user, $ip);
                $isvalid = false;
                break;
            case 3:
                $this->setStatus(self::LOGIN_USER_UNKNOWN, $user, $ip);
                $isvalid = false;
                break;
        }
        $this->notify();
        return $isvalid;
    }
}

interface Observer {
    function update(Observable $observable);
}

abstract class LoginObserver implements Observer {
    private $login;

    function __construct(Login $login) {
        $this->login = $login;
        $login->attach($this);
    }

    function update(Observable $observable) {
        if($observable === $this->login) {
            $this->doUpdate($observable);
        }
    }

    abstract function doUpdate(Login $login);
}

class SecurityMonitor extends LoginObserver {
    function doUpdate(Login $login) {
        $status = $login->getStatus();
        if($status[0] == Login::LOGIN_WRONG_PASS) {
            print __CLASS__ . ": Отправка почты системному администратору<br>";
        }
    }
}

class GeneralLogger extends LoginObserver {
    function doUpdate(Login $login) {
        $status = $login->getStatus();
        print __CLASS__ . ": Регистрация в системном журнале<br>";
    }
}

class PartnershipTool extends LoginObserver {
    function doUpdate(Login $login) {
        $status = $login->getStatus();
        print __CLASS__ . ": Отправка cookie-файла, если адрес соответствует списку<br>";
    }
}
//-------------------------------- RUN
$login = new Login();
new SecurityMonitor($login);
new GeneralLogger($login);
new PartnershipTool($login);

//-------------------------------- DOCUMENTATION
//В основе шаблона Observer лежит принцип отсоединения клиентских элементов
//(наблюдателей) от центрального класса (субъекта). Наблюдатели должны быть
//проинформированы, когда происходят события, о которых знает субъект. В то
//же время мы не хотим, чтобы у субъекта была жестко закодированная связь с
//его классаминаблюдателями. Чтобы достичь этого, мы можем разрешить
//наблюдателям регистрироваться у субъекта. Поэтому мы даем классу Login три
//новых метода - attach(), detach() и notify() - и все это вводим в действие
//с помощью интерфейса ObservaЬle.

//Итак, класс Login управляет списком объектов-наблюдателей. Они моrут быть
//добавлены третьей стороной с помощью метода attach() и удалены с помощью метода
//detach(). Метод notify() вызывается, чтобы сказать наблюдателям о том, что
//произошло что-то интересное. Этот метод просто проходит в цикле по списку наблюдателей,
//вызывая для каждого из них метод update().

//Обратите внимание на то, как объект-наблюдатель использует переданный
//ему экземпляр объекта Observable, чтобы получить дополнительную информацию
//о событии. Класс-субъект должен обеспечить методы, которые могут запросить наблюдатели.
//чтобы узнать о состоянии. В данном случае мы определили метод getstatus(),
//который могут вызывать наблюдатели, чтобы получить информацию о текутуцем состоянии.
//Но это добавление также выявляет проблему. При вызове метода Login::getStatus()
//классу SecurityMoпitor передается больше информации, чем нужно для
//ее безопасного использования. Этот вызов делается для объекта типа Observable ,
//но нет никакой гарантии, что это также будет объект типа Login. Выйти и з такой
//ситуации можно двумя путями. Мы можем расширить интерфейс Observable
//и включить в него объявление метода getStatus(). При этом, вероятно, придется
//переименовать интерфейс во что-то вроде ObservableLogiп. чтобы показать, что он
//характерен для типа Login.
//Есть и второй вариант - сохранить интерфейс Observable общим, но сделать
//так. чтобы классы Observer были ответственными за работу с субъектами правильного
//типа. Они даже могут выполнять работу по присоединению себя к своему
//субъекту. Поскольку у нас будет больше одного типа Observer и мы планируем выполнять
//некоторые операции. общие для них всех, давайте создадим абстрактный
//суперкласс. который будет заниматься этой вспомогательной работой.

//Еще один подход к проблеме взаимодействия между субъектом типа Observable
//и наблюдателем типа Observer состоит в том, что методу update() можно передать
//конкретную информацию о состоянии, а не экземпляр субъекта типа Observable.
//Для быстрого решения я обычно выбирал этот подход. Поэтому в нашем примере
//методу update() должны были бы передаваться код состояния, имя пользователя
//и IР-адрес (вероятно. в массиве, для переносимости), а не экземпляр класса Login.
//Благодаря этому нам не пришлось бы создавать отдельный метод в классе Login для
//получения состояния. С другой стороны, поскольку в классе-субъекте сохраняется
//много информации о состоянии, передача его экземпляра методу update() предоставляет
//наблюдателям гораздо большую степень гибкости.
//Вы можете также полностью зафиксировать тип аргумента, чтобы класс Login
//работал только с классом-наблюдателем определенного типа (вероятно, LoginObserver).
//Для этого в о время выполнения программы предусмотрите проверку типов объектов,
//переданных методу attach(); в противном случае вам. возможно.
//придется вообще пересмотреть интерфейс Observable .
//И снова мы использовали композицию во время выполнения, чтобы построить
//гибкую и расширяемую модель. Класс Login можно извлечь из контекста и включить
//в совершенно другой проект без всяких изменений. И там он сможет работать
//с другим набором наблюдателей.