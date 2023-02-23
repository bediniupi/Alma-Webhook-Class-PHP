<?php
/**
 * Alma_webhook_class.php
 *
 * Alma Ex Libris webhook class: configure a webhook in Alma pointing to php script:
 * <code> 
 *  require("Alma_webhook_class.php");
 *  $webh = new almaWebhook('Secret'); // or $webh = new almaWebhook() if you place secret in class file directly;
 *  if ($webh->result) {
 *       //your code (use $webh->res_obj to collect data; if you prefer arrays, use $dataray = (array)$webh->res_obj, $webh->result contains result as a string);
 *       $action = $webh->res_obj->action // event type in Alma
 *       $webh->addLog("this is happening") // add info to log
 *   }  
 *  file_put_contents("filelog.txt", $webh->log, FILE_APPEND); // save log to a file
 * </code>
 *
 * @author Nazzareno Bedini <nazzareno.bedini@unipi.it>
 * @license MIT
 * @copyright 2020 Nazzareno Bedini, Università di Pisa
*/
class almaWebhook {
    private $secret = "";
    public $result;
    public $res_obj;
    public $log;
    public $sep = "---------------";
    public function __construct($secret="") {
        $this->addLog($this->sep);
        if ($secret) $this->secret = $secret;        
        if (isset($_GET['challenge'])) {
            echo '{"challenge":"'.$_GET['challenge'].'"}​';
            $this->addLog("Challenge Request [".$_GET['challenge']."]");
            $this->result = "";
            return;
            } else {
            $this->result = file_get_contents('php://input');
            if (!isset($_SERVER['HTTP_X_EXL_SIGNATURE']) || !$this->valSign($this->result, $_SERVER['HTTP_X_EXL_SIGNATURE'])) {
                $this->result = "";
                $this->addLog("Invalid Signature\n");
                header('HTTP/1.1 401 Invalid Signature');                 
                return;               
                }
            $this->res_obj = $this->get_obj($this->result);
            }
    }    
    private function valSign($body, $sign) {
        $hash = base64_encode(hash_hmac('sha256', $body, $this->secret, true));
        $this->addLog("hash: ".$hash." - sign: ".$sign);
        if ($hash != $sign) return false;
        return true;
    }
    private function get_obj($res) {
        $par = substr($res, 0, 1);
        switch ($par) {
            case "{":
                $res_obj = json_decode($res);
                $this->addLog("Result type is JSON");
                break;
            case "<":
                $res_obj = simplexml_load_string($res);
                $this->addLog("Result type is XML");
                break;
            default:
                $this->addLog("Result type not recognized (first char:".$par.")");
                $this->addLog("Source:\n".$this->result);
                return false;
            }
        return $res_obj;
    }
    public function addLog($logtext) {
        $this->log .= date("d/m/Y h:i")." - ".$logtext."\n";
    }
}
?>
