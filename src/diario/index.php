<?php
//2021.07.12.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/SimpleTelegramBot

function Command_diario():void{
  global $Bot;
  $Max = 1359;
  $Skip = [3, 1323, 1353, 1355];
  $Img = [1355];
  $Split = true;
  if($Bot->Parameters() === null):
    $Bot->SendPhoto($Bot->ChatId(), __DIR__ . '/images/' . rand(1, 10) . '.png');
    do{
      $n = rand(1, $Max);
    }while(array_search($n, $Skip) !== false);
    $texto = file_get_contents('https://raw.githubusercontent.com/SantuarioMisericordiaRJ/DiarioSantaFaustina/main/' . $n . '.txt');
  elseif($Bot->Parameters() > $Max):
    $Bot->Send($Bot->ChatId(), "Por enquanto, só tenho até o número ". $Max);
  elseif(array_search([$n, $Bot->Parameters()], $Img) !== false):
    $Bot->SendPhoto($Bot->ChatId(), 'https://raw.githubusercontent.com/SantuarioMisericordiaRJ/DiarioSantaFaustina/main/' . $n . '.png');
    $Split = false;
  else:
    $texto = file_get_contents('https://raw.githubusercontent.com/SantuarioMisericordiaRJ/DiarioSantaFaustina/main/' . $Bot->Parameters() . '.txt');
  endif;
  if($Split):
    foreach(str_split($texto, TelegramBot::MsgSizeLimit) as $texto):
      $Bot->Send($Bot->ChatId(), $texto);
    endforeach;
  endif;
  LogEvent('diario');
}