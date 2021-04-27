<?php
//2021.04.23.00
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/SimpleTelegramBot

function Command_diario():void{
  global $Bot;
  $texto = explode(' ', $Bot->Msg());
  if(isset($texto[1])):
    $texto = file_get_contents('https://raw.githubusercontent.com/SantuarioMisericordiaRJ/DiarioSantaFaustina/main/' . $texto[1] . '.txt');
  else:
    $Bot->SendPhoto($Bot->ChatId(), __DIR__ . '/images/' . rand(1, 10) . '.png');
    $texto = file_get_contents('https://raw.githubusercontent.com/SantuarioMisericordiaRJ/DiarioSantaFaustina/main/' . rand(90, 909) . '.txt');
  endif;
  foreach(str_split($texto, TelegramBot::MsgSizeLimit) as $texto):
    $Bot->Send($Bot->ChatId(), $texto);
  endforeach;
  LogEvent('diario');
}