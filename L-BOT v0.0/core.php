<?php

require_once('configs/config.php');
require_once('inc/classes/ts3admin.class.php');



$instance = getopt("i:");


if(empty($config[$instance['i']])){
  echo "Nie ma takiej instancji!".PHP_EOL;
  exit;
}



if($config['database']['enabled']){
  try
  {
   $dsn = $config['database']['type'] .
   ':host=' . $config['database']['ip'] .
   ';port=' . $config['database']['port'] .
   ';encoding=' . $config['database']['encoding'] .
   ';dbname=' . $config['database']['dbname'];
   $baza = new PDO($dsn, $config['database']['login'], $config['database']['passwd']);
   $baza->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   define('DB_CONNECTED', true);
   echo "Pomyślnie połączono z baza danych".PHP_EOL;
  } catch(PDOException $e)
  {
   die("nie mozna pol z baza danych" . $e->getMessage());
  }
}


$ts = new query($config[$instance['i']]['conn']['ip'], $config[$instance['i']]['conn']['queryPort']);
if($ts->getElement('success', $ts->connect())){
  echo "Pomyślnie połączono z serwerem!".PHP_EOL;
  if($ts->getElement('success', $ts->login($config[$instance['i']]['conn']['login'], $config[$instance['i']]['conn']['passwd']))){
    echo "Pomyślnie zalogowano na query!".PHP_EOL;
    if($ts->getElement('success', $ts->selectServer($config[$instance['i']]['conn']['voicePort']))){
      echo "Pomyślnie wybrano serwer!".PHP_EOL;
    }
    else{
      echo "Bot nie mógł wybrać serwera. Sprawdź plik konfiguracyjny!".PHP_EOL;
      exit;
    }
    $cid = $ts->getElement('data', $ts->clientInfo($ts->getElement('data',$ts->whoAmI())['client_id']))['cid'];
    if($cid != $config[$instance['i']]['conn']['channelId']){
      if($ts->getElement('success', $ts->clientMove($ts->getElement('data',$ts->whoAmI())['client_id'], $config[$instance['i']]['conn']['channelId']))){
        echo "Bot zmienił kanał na".PHP_EOL;
      }
      else{
        echo "Bot nie zmienił kanału".PHP_EOL;
      }
    }
  }
  else{
    "Bot nie mógł zalogować się na query".PHP_EOL;
  }


  foreach($config[$instance['i']]['events']['enabled'] as $e){
    require_once('inc/functions/events/'.$instance['i'].'/'.$e.".php");
  }
  foreach($config[$instance['i']]['plugins']['enabled'] as $p){
    require_once('inc/functions/events/'.$instance['i'].'/'.$p.".php");
  }


  while(true){


    if(!empty($config[$instance['i']]['events']['enabled'])){

      foreach($config[$instance['i']]['events']['enabled'] as $eventName){


        if(empty($$eventName) || $$eventName < time()){

          $load = new $eventName;
		  $load->start($test);


          $$eventName = time()+$config[$instance['i']]['events']['intervals'][$eventName];
        }
      }
    }

   
    if(!empty($config[$instance['i']]['plugins']['enabled'])){
      foreach($config[$instance['i']]['events']['enabled'] as $pluginName){
        $load = new $pluginName;
		$load->start($ts);
      }
    }
  }
}
else{
  echo "Nie można połączyc z serwerem!".PHP_EOL;
}
 ?>
