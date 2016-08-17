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

$login = new Login();
new SecurityMonitor($login);
new GeneralLogger($login);
new PartnershipTool($login);
//266