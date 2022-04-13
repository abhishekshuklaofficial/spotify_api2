<?php 
// declare(strict_types=1);

namespace App\Console;
use Phalcon\Cli\Task;
use Phalcon\Http\Request;
use Phalcon\Escaper;
use Phalcon\Mvc\Controller;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;

class MainTask extends Task
{
    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;
    }
    public function logAction()
    {
        unlink('../app/logs/log.log');
    }
    public function adminAction()
    {
        $signer  = new Hmac();

        // Builder object
        $builder = new Builder($signer);

        $now        = 's';
        $issued     = $now->getTimestamp();
        $notBefore  = $now->modify('-1 minute')->getTimestamp();
        $expires    = $now->modify('+1 day')->getTimestamp();
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';
        $key = "key";

        $payload = array(
            "iss" => $this->url->getBaseUri(),
            "aud" => $this->url->getBaseUri(),
            "iat" => $issued,
            "nbf" => $notBefore,
            "exp" => $expires,
            "role" => $check
        );
        // print_r($payload);
        $token = JWT::encode($payload, $key, 'HS256');
        echo $token;
    }
}