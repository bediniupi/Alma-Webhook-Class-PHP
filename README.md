# Alma-Webhook-Class-PHP
A class to use Alma Ex Libris webhooks in php scripts 

* You can use both JSON and XML output
* Challenge and Signature verification
* Logging system

## Usage
Example code  
```
<?php
require("Alma_webhook_class.php");
$webh = new almaWebhook('Secret'); // or $webh = new almaWebhook() if you place secret in class file directly;
if ($webh->result) {
    //your code (use $webh->res_obj to collect data; if you prefer arrays, use $dataray = (array)$webh->res_obj, $web->result contains result as a string);
    $action = $webh->res_obj->action // event type in Alma    
    $webh->addLog("this is happening") // add info to log
  } 
file_put_contents("filelog.txt", $webh->log, FILE_APPEND); // save log to a file
?>
```
## Prerequisites
* PHP, Web Server 
* Alma Ex Libris

## Authors
* **Nazzareno Bedini - University of Pisa**

## References
* \[1\] [Alma webhooks](https://knowledge.exlibrisgroup.com/Alma/Product_Documentation/010Alma_Online_Help_(English)/090Integrations_with_External_Systems/030Resource_Management/300Webhooks).
