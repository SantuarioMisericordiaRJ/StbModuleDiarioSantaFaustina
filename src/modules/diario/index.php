<?php
//Protocol Corporation Ltda.
//https://github.com/SantuarioMisericordiaRJ/StbModuleDiarioSantaFaustina
//2022.03.04.01

const DiarioUrl = 'https://raw.githubusercontent.com/SantuarioMisericordiaRJ/DiarioSantaFaustina/main/src';
const DiarioMax = 1828;
const DiarioSkip = [3, 1323, 1353, 1355, 1590];
const DiarioImg = [1355];

const DiarioInscritos = 0;

function Command_diario():void{
  /**
   * @var TblCmd $Webhook
   * @var TelegramBotLibrary $Bot
   */
  DebugTrace();
  global $Webhook, $Bot;
  $Split = true;
  $Bot->SendAction($Webhook->Chat->Id, TgChatAction::Typing);
  if($Webhook->Parameters === null):
    DiarioEnviaFoto(rand(0, 9));
    do{
      $n = rand(1, DiarioMax);
    }while(array_search($n, DiarioSkip) !== false);
    if(array_search($n, DiarioImg) !== false):
      DiarioEnviaFoto($n);
      $Split = false;
    else:
      $texto = file_get_contents(DiarioUrl . '/' . $n . '.txt');
    endif;
  elseif($Webhook->Parameters > DiarioMax):
    $Bot->SendText(
      $Webhook->Chat->Id,
      "Por enquanto, só tenho até o número ". DiarioMax
    );
    $Split = false;
  elseif(array_search($Webhook->Parameters, DiarioImg) !== false):
    DiarioEnviaFoto($Webhook->Parameters);
    $Split = false;
  else:
    $texto = file_get_contents(DiarioUrl . '/' . trim($Webhook->Parameters) . '.txt');
  endif;
  if($Split):
    foreach(str_split($texto, TblConstants::LimitText) as $texto):
      $Bot->SendText(
        $Webhook->Chat->Id,
        $texto,
        ParseMode: TgParseModes::Html
      );
    endforeach;
  endif;
  if($Webhook->Parameters === null):
    LogEvent('diario', 'Aleatório: ' . $n);
  else:
    LogEvent('diario', $Webhook->Parameters);
  endif;
}

function Command_diarioon():void{
  /**
   * @var TblCmd $Webhook
   * @var TelegramBotLibrary $Bot
   */
  global $Webhook;
  DebugTrace();
  $DbDiario = new StbDb(DirToken, 'Diario');
  $db = $DbDiario->Load();
  $db[DiarioInscritos][$Webhook->User->Id] = time();
  $DbDiario->Save($db);
  $Bot->SendText(
    $Webhook->User->Id,
    'Você <b>ativou</b> o envio diário de uma passagem aleatória do Diário de Santa Faustina. Para desativar, use o comando /diariooff.',
    ParseMode: TgParseModes::Html
  );
  LogEvent('diarioon');
}

function Command_diariooff():void{
  /**
   * @var TblCmd $Webhook
   * @var TelegramBotLibrary $Bot
   */
  global $Webhook, $Bot;
  DebugTrace();
  $DbDiario = new StbDb(DirToken, 'Diario');
  $db = $DbDiario->Load();
  unset($db[DiarioInscritos][$Webhook->User->Id]);
  $DbDiario->Save($db);
  $Bot->SendText(
    $Webhook->User->Id,
    'Você <b>desativou</b> o envio diário de uma passagem aleatória do Diário de Santa Faustina. Para re-ativar, use o comando /diarioon.',
    ParseMode: TgParseModes::Html
  );
  LogEvent('diariooff');
}

function Cron_Diario():void{
  /**
   * @var TblCmd $Webhook
   * @var TelegramBotLibrary $Bot
   */
  global $Bot;
  DebugTrace();
  $DbDiario = new StbDb(DirToken, 'Diario');
  $db = $DbDiario->Load();
  foreach(($db[DiarioInscritos] ?? []) as $user => $dia):
    DiarioEnviaFoto(rand(0, 9));
    do{
      $n = rand(1, DiarioMax);
    }while(array_search($n, DiarioSkip) !== false);
    $texto = file_get_contents(DiarioUrl . '/' . $n . '.txt');
    foreach(str_split($texto, TblConstants::LimitText) as $texto):
      $Bot->SendText(
        $user,
        $texto,
        ParseMode: TgParseModes::Html
      );
    endforeach;
  endforeach;
}

function DiarioEnviaFoto(int $Id){
  /**
   * @var TblCmd $Webhook
   * @var TelegramBotLibrary $Bot
   */
  global $Webhook, $Bot;
  $File = __DIR__ . '/cache.json';
  if(is_file($File)):
    $Cache = file_get_contents($File);
    $Cache = json_decode($Cache, true);
  else:
    $Cache = [];
  endif;
  if(isset($Cache[$Id])):
    $return = $Bot->SendPhoto(
      $Webhook->Chat->Id,
      $Cache[$Id],
      DisableNotification: true
    );
  else:
    $return = $Bot->SendPhoto(
      $Webhook->Chat->Id,
      dirname($_SERVER['SCRIPT_URI'], 2) . '/modules/diario/images/' . $Id . '.jpg',
      DisableNotification: true
    );
    $Cache[$Id] = $return->Photo[count($return->Photo) - 1]->Id;
    file_put_contents($File, json_encode($Cache));
  endif;
}