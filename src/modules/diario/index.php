<?php
//2021.09.21.02
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive/SimpleTelegramBot

function Command_diario(TblCmd $Webhook):void{
  DebugTrace();
  global $Bot;
  $Url = 'https://raw.githubusercontent.com/SantuarioMisericordiaRJ/DiarioSantaFaustina/main/src';
  $Max = 1699;
  $Skip = [3, 1323, 1353, 1355, 1590];
  $Img = [1355];
  $Split = true;
  $Webhook->ReplyAction(TblActions::Typing);
  if($Webhook->Parameters === null):
    $Webhook->ReplyPhoto(dirname($_SERVER['SCRIPT_URI'], 2) . '/modules/diario/images/' . rand(1, 10) . '.png', null, null, null, null, null, false);
    do{
      $n = rand(1, $Max);
    }while(array_search($n, $Skip) !== false);
    if(array_search($n, $Img) !== false):
      $Webhook->ReplyPhoto($Url . '/' . $n . '.png');
      $Split = false;
    else:
      $texto = file_get_contents($Url . '/' . $n . '.txt');
    endif;
  elseif($Webhook->Parameters > $Max):
    $Webhook->ReplyMsg("Por enquanto, só tenho até o número ". $Max);
    $Split = false;
  elseif(array_search($Webhook->Parameters, $Img) !== false):
    $Webhook->ReplyPhoto($Url . '/' . $Webhook->Parameters . '.png');
    $Split = false;
  else:
    $texto = file_get_contents($Url . '/' . $Webhook->Parameters . '.txt');
  endif;
  if($Split):
    foreach(str_split($texto, TblConstants::MsgSizeLimit) as $texto):
      $Webhook->ReplyMsg($texto, null, null, TblParse::Html);
    endforeach;
  endif;
  if($Webhook->Parameters === null):
    LogEvent($Webhook, 'diario', 'Aleatório: ' . $n);
  else:
    LogEvent($Webhook, 'diario', $Webhook->Parameters);
  endif;
}