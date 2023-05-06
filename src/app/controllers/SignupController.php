<?php
use Phalcon\Mvc\Controller;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

session_start();
class SignupController extends Controller
{
    public function indexAction()
    {
        // redirected to view
    }

    public function addAction()
    {
        $arr = array(
            'mail' => $this->escaper->escapeHTML($this->request->getPost('email')),
            'pass' => $this->escaper->escapeHTML($this->request->getPost('password')),
            'role' => $this->escaper->escapeHTML($this->request->getPost('role'))
        );
        if ($_POST['repassword'] == $_POST['password']) {
            $this->db->execute(
                "INSERT INTO `users`(`email`, `password`, `role`)
                VALUES ('$arr[mail]', '$arr[pass]', '$arr[role]')"
            );
            $this->response->redirect('signup/login');
        } else {
            $adapter = new Stream(APP_PATH.'/storage/logs/main.log');
            $logger = new Logger(
                'messages',
                [
                    'main' => $adapter,
                ]
            );
            $logger->error("Failed Signup attempt.
            Email => $_POST[email],
            Password => $_POST[password],
            Repassword => $_POST[repassword]
            Role => $_POST[role] \n
            ");
            $this->response->redirect('signup/');
        }
    }
    public function loginAction()
    {
        // redirected to view
    }
    public function findloginAction()
    {
        if (isset($_POST)) {
            $arr = array(
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password')
            );
            $user = $this->db->fetchAll(
                "SELECT * FROM `users` WHERE `email` = '$arr[email]' AND `password` = '$arr[password]'",
                \Phalcon\Db\Enum::FETCH_ASSOC
            );
            $_SESSION['user'] = $user[0]['id'];
            $_SESSION['role'] = $user[0]['role'];
            if (isset($user[0])) {
                $this->response->redirect('/product/');
                // Defaults to 'sha512'
                $signer = new Hmac();

                // Builder object
                $builder = new Builder($signer);
                $now = new DateTimeImmutable();
                $issued = $now->getTimestamp();
                $notBefore = $now->modify('-1 minute')->getTimestamp();
                $expires = $now->modify('+1 day')->getTimestamp();
                $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';
                $builder
                    ->setAudience('https://target.phalcon.io') // aud
                    ->setContentType('application/json') // cty - header
                    ->setExpirationTime($expires) // exp
                    ->setId('abcd123456789') // JTI id
                    ->setIssuedAt($issued) // iat
                    ->setIssuer('https://phalcon.io') // iss
                    ->setNotBefore($notBefore) // nbf
                    ->setSubject($_SESSION['role']) // sub
                    ->setPassphrase($passphrase); // password
                $tokenObject = $builder->getToken();
                // The token
                $_SESSION['currToken'] = $tokenObject->getToken();
            } else {
                $this->response->redirect('signup/login');
            }
        }
    }
    public function logoutAction()
    {
        session_unset();
        session_destroy();
        $this->response->redirect('/signup/');
    }
}