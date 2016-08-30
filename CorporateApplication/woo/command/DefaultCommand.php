<?php
namespace woo\command;

class DefaultCommand extends Command{
    function doExecute(\woo\controller\Request $request) {
        $request->addFeedback('Добро пожаловать в Woo!');
        include('woo/view/main.php');
    }
}