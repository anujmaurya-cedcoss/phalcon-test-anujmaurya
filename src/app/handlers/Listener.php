<?php
namespace handler\Listener;

use Phalcon\Acl\Adapter\Memory;
use Phalcon\Mvc\Application;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Di\Injectable;
use Phalcon\Security\JWT\Token\Parser;

session_start();
class Listener extends injectable
{
    public function beforeHandleRequest(Event $event, Application $app, Dispatcher $dis)
    {
        $acl = new Memory();
        /*
         * Add the roles
         */

        $acl->addRole('admin');
        $acl->addRole('manager');
        $acl->addRole('user');
        $acl->addRole('accountant');
        $acl->addRole('guest');
        /*
         * Add the Components
         */

        $acl->addComponent('index', ['index',]);
        $acl->addComponent('order', ['index']);
        $acl->addComponent('product', ['add', 'index', 'edit', 'myorders', 'productcrud', 'buy']);
        $acl->addComponent('role', ['index', 'add',]);
        $acl->addComponent('setting', ['index',]);
        $acl->addComponent('signup', ['index', 'login']);
        $acl->addComponent('signup', ['index', 'edit']);

        $acl->allow('admin', '*', '*');
        $acl->allow('manager', 'product', '*');
        $acl->allow('accountant', 'order', '*');
        $acl->allow('*', 'index', 'index');
        $acl->allow('user', 'product', ['index', 'myorders', 'buy']);
        $acl->allow('guest', 'product', ['index']);
        $acl->allow('*', 'signup', '*');
        $controller = $dis->getControllerName();
        $action = $dis->getActionName();
        if ($controller == '') {
            $controller = 'index';
        }
        if ($action == '') {
            $action = 'index';
        }
        $tokenReceived = $_SESSION['currToken'];
        if ($tokenReceived != '' && isset($_SESSION['currToken'])) {
            $parser = new Parser();
            $tokenObject = $parser->parse($tokenReceived);
            $role = $tokenObject->getClaims()->getPayload()['sub'];
        }
        if ($role == '') {
            $role = 'guest';
        }
        if (true === $acl->isAllowed($role, $controller, $action)) {
            if (file_exists(APP_PATH . "/controllers/$controller/")) {
                $_SESSION['currUser'] = $tokenReceived;
                $this->response->redirect($controller / $action);
            } else {
                echo ('Access Granted') . ':)';
            }
        } else {
            echo ('Access denied') . ':(';
            die;
        }
    }
}
