<?php
//2021.06.27.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/SimpleTelegramBot

function Command_diario():void{
  global $Bot;
  $skip = [3];
  if($Bot->Parameters() === null):
    $Bot->SendPhoto($Bot->ChatId(), __DIR__ . '/images/' . rand(1, 10) . '.png');
    do{
      $n = rand(1, 1279);
    }while(array_search($n, $skip) !== false);
    $texto = file_get_contents('https://raw.githubusercontent.com/SantuarioMisericordiaRJ/DiarioSantaFaustina/main/' . $n . '.txt');
  else:
    $texto = file_get_contents('https://raw.githubusercontent.com/SantuarioMisericordiaRJ/DiarioSantaFaustina/main/' . $Bot->Parameters() . '.txt');
  endif;
  foreach(str_split($texto, TelegramBot::MsgSizeLimit) as $texto):
    $Bot->Send($Bot->ChatId(), $texto);
  endforeach;
  LogEvent('diario');
}