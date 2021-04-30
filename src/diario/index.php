<?php
//2021.04.30.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/SimpleTelegramBot

function Command_diario():void{
  global $Bot;
  if($Bot->Parameters() === null):
    $Bot->SendPhoto($Bot->ChatId(), __DIR__ . '/images/' . rand(1, 10) . '.png');
    $texto = file_get_contents('https://raw.githubusercontent.com/SantuarioMisericordiaRJ/DiarioSantaFaustina/main/' . rand(50, 909) . '.txt');
  else:
    $texto = file_get_contents('https://raw.githubusercontent.com/SantuarioMisericordiaRJ/DiarioSantaFaustina/main/' . $Bot->Parameters() . '.txt');
  endif;
  foreach(str_split($texto, TelegramBot::MsgSizeLimit) as $texto):
    $Bot->Send($Bot->ChatId(), $texto);
  endforeach;
  LogEvent('diario');
}