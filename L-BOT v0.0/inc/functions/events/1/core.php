<?php
/*
  Ładujemy wszystkie potrzebne pliki
*/
require_once('configs/config.php');
require_once('inc/classes/ts3admin.class.php');


/*
    Pobieramy numer instancji z komendy startowej "php core.php -i 1"
    Czyli teraz zmienna $instance['i'] przyjmie wartość 1
*/
$instance = getopt("i:");

/*
  Teraz sprawdzamy czy jest podana instancja
  Jak już wcześniej napisałem zmienna $instance['i'] przyjmie wartość 1
  W configu mamy utworzoną 1 instancję -> $config[1]
  Więc $config[$instance['i']] to jest to samo co $config[1]
*/
if(empty($config[$instance['i']])){
  echo "Nie ma takiej instancji!".PHP_EOL;
  exit;
}

/*
  Tutaj inicjujemy połączenie z bazą danych
  Nie ma tu co tłumaczyć bo to rzecz oczywista
  Dane są pobrane oczywiście z configu
*/
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

/*
  Tworzymy obiekt $ts
  I oczywiście zmienne z danymi są tu wstawione czyli IP, Port query itp...
  Tutaj dalej nie będę tłumaczył bo połączenie bota z ts. To już klasa ts3admin i jak ktoś tego nie zna niech się najpierw nauczy ts3admin.class :)
*/
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

  /*
    Teraz ładujemy wszystkie funkcje, które zostały wymienione w configu jako te, które maja byc włączone.
  */
  foreach($config[$instance['i']]['events']['enabled'] as $e){
    require_once('inc/functions/events/'.$instance['i'].'/'.$e.".php");
  }
  foreach($config[$instance['i']]['plugins']['enabled'] as $p){
    require_once('inc/functions/events/'.$instance['i'].'/'.$p.".php");
  }

  /*
    Przechodzimy do właściwej części kodu
  */
  while(true){

    /*
      Tu się robi ciekawie
      Najpierw sprawdzamy czy lista funkcji (eventów) do wykonania nie jest pusta
    */
    if(!empty($config[$instance['i']]['events']['enabled'])){

      # Teraz rozbijamy tablicę z włączonymi funkcjami
      foreach($config[$instance['i']]['events']['enabled'] as $eventName){

        /*
          Teraz robimy takie coś
          Zmienna $eventName to nazwa naszej funkcji np. test
          Czas funckji będziemy zapisywać w zmiennej $$eventName
          Zauważ, że dałem dwa "dolary"
          $$eventName jest to inna zmienna niż $eventName
        */
        if(empty($$eventName) || $$eventName < time()){

          # tutaj wywołujemy poszczególnie funkcje
          new $test($ts);

          /*
            Zapisujemy w zmiennej $$eventName czas wykonania funkcji + interwał
            Dzięki temu wyżej w tym warunku -> if(empty($$eventName) || $$eventName < time())
            A dokładnie w tym -> $$eventName < time()
            Sprawdzamy czy możemy wykonać już daną funkcję
          */
          $$eventName = time()+$config[$instance['i']]['events']['intervals'][$eventName];
        }
      }
    }

    /*
      Tutaj to już jest łatwo
    */
    if(!empty($config[$instance['i']]['plugins']['enabled'])){
      foreach($config[$instance['i']]['events']['enabled'] as $pluginName){
        new $pluginName($ts);
      }
    }
  }
}
else{
  echo "Nie można połączyc z serwerem!".PHP_EOL;
}
 ?>
