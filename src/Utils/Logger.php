<?php
/**
 * Created by PhpStorm.
 * User: momus
 * Date: 7/14/18
 * Time: 3:22 PM
 */

namespace App\Utils;


use App\Entity\Users;

class Logger
{
    /**
     * @var
     */
    private $response;

    /**
     * @param Users $user
     *
     * @return bool
     */
    public function logInAs(Users $user): bool
    {
        $url = "https://www.kartamultisport.pl/moj-profil?user={$user->getLogin()}&pass={$user->getPassword()}&submit=Zaloguj&logintype=login&pid=561%2C531&redirect_url=%2F&tx_felogin_pi1%5Bnoredirect%5D=1";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $this->response = curl_exec($ch);
        curl_close($ch);

        return (strpos($this->response, 'Login i hasło nie pasują do siebie') !== false) ? false : true;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}