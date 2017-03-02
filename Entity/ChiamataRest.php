<?php
/**
 * Created by PhpStorm.
 * User: bruno
 * Date: 11/2/16
 * Time: 12:41 PM
 */

namespace Services\Bundle\Rest\Entity;

/**
 * This class permit to call rest resources
 *
 * Class ChiamataRest
 * @package ServicesBundle\Entity
 */
class ChiamataRest
{

    /**
     * Url to call
     *
     * @string
     */
    private $url;

    /**
     * Userid for the header
     *
     * @string
     */
    private $login;

    /**
     * Password for the header
     *
     * @string
     */
    private $password;

    /**
     * Who is calling
     *
     * @string
     */
    private $chiamante;

    /**
     * Here you can set your json
     *
     * @string
     */
    private $json;

    /**
     * http verrb
     *
     * @string
     */
    private $tipoChiamata;

    /**
     * Contain the information if needs to control success field or not in case it doesn't exist
     *
     * @boolean
     */
    private $controlSuccess=true;

    /**
     *
     * Questo metodo effettua una chiamata rest con decodifica in output sotto autenticazione all'url passato, restituisce un array decodificato dal json di risposta, controlla anche se c'è stato un errore logico ed in caso solleva un'eccezione
     * Se viene passato un tipo chiamata questo può assumere i seguenti valori
     * GET
     * POST
     * PUT
     * DELETE
     *
     * This metod make a rest call and return an array, http verb permits are:
     * GET
     * POST
     * PUT
     * DELETE
     *
     * @return array
     * @throws \Exception
     */
    public function chiamataRestDecodificata() {

        //Dichiaro le variabili
        $url=$this->url;
        $login=$this->login;
        $password=$this->password;
        $chiamante=$this->chiamante;
        $json=$this->json;
        $tipoChiamata=$this->tipoChiamata;
        $ritorno="";
        $jsonDecodificato="";

        //Tolgo gli spazi
        $url = str_replace(" ","%20",$url);

        //Inizializzo la chiamata
        $ch = curl_init();

        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        //Imposto i valori
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $tipoChiamata);

        //Se è post o patch di default deve passargli un json
        if ($tipoChiamata=="POST" || $tipoChiamata=="PUT") {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json))
            );
        }

        //Se è stato passato un userid allora lo setto nell'header
        if (!empty($login)) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
        }

        //Gli passo il json se valorizzato
        if (!empty($json)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        }

        //Effettuo la chiamata
        $ritorno=curl_exec($ch);

        // Check if an error occurred
        if(curl_errno($ch)) {
            curl_close($ch);
            throw new \Exception("Risposta negativa alla seguente chiamata:".$chiamante." Il messaggio di ritorno è:".$ritorno);
        }

        // Get HTTP response code
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //Chiudo la chiamata
        curl_close($ch);

        //Controllo se il codice è tra quelli ammessi (200,201,202)
        if ($code!=200 && $code!=201 && $code!=202)
            throw new \Exception("Risposta negativa alla seguente chiamata:".$chiamante." Il codice di ritorno è:".$code." e il messaggio:".$ritorno);

        //Decodifico in un array il json di ritorno
        $jsonDecodificato=json_decode($ritorno);

        //Controllo se è stato scelto di testare il success field
        if ($this->controlSuccess) {
            //Controllo se il campo success è true o false
            if (!$jsonDecodificato->success) {
                throw new \Exception("Risposta negativa alla seguente chiamata:".$chiamante.". Le informazioni restituite dal Web Service sono le seguenti:".
                    $jsonDecodificato->message);
            }
        }

        //Restituisco l'array relativo al json ricevuto
        return $jsonDecodificato;
    }

    /**
     *
     * Questo metodo effettua una chiamata rest sotto autenticazione all'url passato, restituisce un array decodificato dal json di risposta, controlla anche se c'è stato un errore logico ed in caso solleva un'eccezione
     * Se viene passato un tipo chiamata questo può assumere i seguenti valori
     * GET
     * POST
     * PUT
     *
     * This metod make a rest call and return a string containing the json received, http verb permits are:
     * GET
     * POST
     * PUT
     * DELETE
     *
     * @return mixed|string
     * @throws \Exception
     */
    public function chiamataRest() {

        //Dichiaro le variabili
        $url=$this->url;
        $login=$this->login;
        $password=$this->password;
        $chiamante=$this->chiamante;
        $json=$this->json;
        $tipoChiamata=$this->tipoChiamata;
        $ritorno="";
        $jsonDecodificato="";

        //Tolgo gli spazi
        $url = str_replace(" ","%20",$url);

        //Inizializzo la chiamata
        $ch = curl_init();

        //Imposto i valori
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $tipoChiamata);

        //Se è post o patch di default deve passargli un json
        if ($tipoChiamata=="POST" || $tipoChiamata=="PUT") {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($json))
            );
        }

        //Se è stato passato un userid allora lo setto nell'header
        if (!empty($login)) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
        }

        //Gli passo il json se valorizzato
        if (!empty($json)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        }

        //Effettuo la chiamata
        $ritorno=curl_exec($ch);

        // Check if an error occurred
        if(curl_errno($ch)) {
            curl_close($ch);
            throw new \Exception("Risposta negativa alla seguente chiamata:".$chiamante." Il messaggio di ritorno è:".$ritorno);
        }

        // Get HTTP response code
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //Chiudo
        curl_close($ch);

        //Controllo se il codice è tra quelli ammessi (200,201,202)
        if ($code!=200 && $code!=201 && $code!=202)
            throw new \Exception("Risposta negativa alla seguente chiamata:".$chiamante." Il codice di ritorno è:".$code." e il messaggio:".$ritorno);

        //Controllo se è stato scelto di testare il success field
        if ($this->controlSuccess) {

            //Decodifico il json in un array per semplicità per accedere meglio alle proprietà successivamente
            $jsonDecodificato=json_decode($ritorno);

            //Controllo il campo success
            if (!$jsonDecodificato->success) {
                throw new \Exception("Risposta negativa alla seguente chiamata:".$chiamante.". Le informazioni restituite dal Web Service sono le seguenti:".
                    $jsonDecodificato->message);
            }
        }

        //Restituisco il json
        return $ritorno;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getChiamante()
    {
        return $this->chiamante;
    }

    /**
     * @param string $chiamante
     */
    public function setChiamante($chiamante)
    {
        $this->chiamante = $chiamante;
    }

    /**
     * @return string
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @param string $json
     */
    public function setJson($json)
    {
        $this->json = $json;
    }

    /**
     * @return string
     */
    public function getTipoChiamata()
    {
        return $this->tipoChiamata;
    }

    /**
     * @param string $tipoChiamata
     */
    public function setTipoChiamata($tipoChiamata)
    {
        $this->tipoChiamata = $tipoChiamata;
    }

    /**
     * @return boolean
     */
    public function getControlSuccess()
    {
        return $this->controlSuccess;
    }

    /**
     * @param boolean $controlSuccess
     */
    public function setControlSuccess($controlSuccess)
    {
        $this->controlSuccess = $controlSuccess;
    }


}