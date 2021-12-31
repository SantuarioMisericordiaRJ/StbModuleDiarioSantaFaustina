<?php
//2021.12.31.00
//Protocol Corporation Ltda.
//https://github.com/SantuarioMisericordiaRJ/StbModuleDiarioSantaFaustina

const DiarioUrl = 'https://raw.githubusercontent.com/SantuarioMisericordiaRJ/DiarioSantaFaustina/main/src';
const DiarioMax = 1828;
const DiarioSkip = [3, 1323, 1353, 1355, 1590];
const DiarioImg = [1355];

const DiarioInscritos = 0;

function Command_diario():void{
  DebugTrace();
  global $Webhook;
  $Split = true;
  $Webhook->ReplyAction(TblActions::Typing);
  if($Webhook->Parameters === null):
    $Webhook->ReplyPhoto(dirname($_SERVER['SCRIPT_URI'], 2) . '/modules/diario/images/' . rand(1, 10) . '.png', null, null, null, null, true, false, false);
    do{
      $n = rand(1, DiarioMax);
    }while(array_search($n, DiarioSkip) !== false);
    if(array_search($n, DiarioImg) !== false):
      $Webhook->ReplyPhoto(DiarioUrl . '/' . $n . '.png');
      $Split = false;
    else:
      $texto = file_get_contents(DiarioUrl . '/' . $n . '.txt');
    endif;
  elseif($Webhook->Parameters > DiarioMax):
    $Webhook->ReplyMsg("Por enquanto, só tenho até o número ". DiarioMax);
    $Split = false;
  elseif(array_search($Webhook->Parameters, DiarioImg) !== false):
    $Webhook->ReplyPhoto(DiarioUrl . '/' . $Webhook->Parameters . '.png');
    $Split = false;
  else:
    $texto = file_get_contents(DiarioUrl . '/' . trim($Webhook->Parameters) . '.txt');
  endif;
  if($Split):
    foreach(str_split($texto, TblConstants::LimitMsg) as $texto):
      $Webhook->ReplyMsg($texto, null, null, TblParse::Html);
    endforeach;
  endif;
  if($Webhook->Parameters === null):
    LogEvent('diario', 'Aleatório: ' . $n);
  else:
    LogEvent('diario', $Webhook->Parameters);
  endif;
}

function Command_diarioon():void{
  DebugTrace();
  global $Webhook;
  $DbDiario = new StbDb(DirToken, 'Diario');
  $db = $DbDiario->Load();
  $db[DiarioInscritos][$Webhook->User->Id] = time();
  $DbDiario->Save($db);
  $Webhook->ReplyMsg('Você <b>ativou</b> o envio diário de uma passagem aleatória do Diário de Santa Faustina. Para desativar, use o comando /diariooff.', null, null, TblParse::Html);
  LogEvent('diarioon');
}

function Command_diariooff():void{
  DebugTrace();
  global $Webhook;
  $DbDiario = new StbDb(DirToken, 'Diario');
  $db = $DbDiario->Load();
  unset($db[DiarioInscritos][$Webhook->User->Id]);
  $DbDiario->Save($db);
  $Webhook->ReplyMsg('Você <b>desativou</b> o envio diário de uma passagem aleatória do Diário de Santa Faustina. Para re-ativar, use o comando /diarioon.', null, null, TblParse::Html);
  LogEvent('diariooff');
}

function Cron_Diario():void{
  DebugTrace();
  global $Bot;
  $DbDiario = new StbDb(DirToken, 'Diario');
  $db = $DbDiario->Load();
  foreach(($db[DiarioInscritos] ?? []) as $user => $dia):
    $Bot->SendPhoto(
      $user,
      $_GET['Site'] . '/modules/diario/images/' . rand(1, 10) . '.png',
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      false
    );
    do{
      $n = rand(1, DiarioMax);
    }while(array_search($n, DiarioSkip) !== false);
    $texto = file_get_contents(DiarioUrl . '/' . $n . '.txt');
    foreach(str_split($texto, TblConstants::LimitMsg) as $texto):
      $Bot->SendMsg(
        $user,
        $texto,
        null,
        null,
        TblParse::Html
      );
    endforeach;
  endforeach;
}