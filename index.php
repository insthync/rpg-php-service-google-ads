<?php
$actions['google-rewarded-ad'] = function($params, $postBody) {
    $gameData = \Base::instance()->get('GameData');
    $output = array('error' => '');
    $player = GetPlayer();
    $playerId = $player->id;

    $id = $postBody['id'];
    $amount = $postBody['amount'];

    $rewardData = $gameData['googleAdRewards'][$id];
    if (!$rewardData) {
        $output['error'] = 'ERROR_INVALID_ITEM_DATA';
    } else {
        // Update staminas
        $updateStaminas = array();
        $stageStamina = GetStamina($playerId, $gameData['stageStaminaId']);
        $stageStamina->amount += $rewardData['rewardStageStamina'] * $amount;
        $stageStamina->update();
        $updateStaminas[] = $stageStamina;
        $arenaStamina = GetStamina($playerId, $gameData['arenaStaminaId']);
        $arenaStamina->amount += $rewardData['rewardArenaStamina'] * $amount;
        $arenaStamina->update();
        $updateStaminas[] = $arenaStamina;
        $output['updateStaminas'] = CursorsToArray($updateStaminas);

        // Update currencies
        $updateCurrencies = array();
        $hardCurrency = GetCurrency($playerId, $gameData['hardCurrencyId']);
        $hardCurrency->amount += $rewardData['rewardHardCurrency'] * $amount;
        $hardCurrency->update();
        $updateCurrencies[] = $hardCurrency;
        $softCurrency = GetCurrency($playerId, $gameData['softCurrencyId']);
        $softCurrency->amount += $rewardData['rewardSoftCurrency'] * $amount;
        $softCurrency->update();
        $updateCurrencies[] = $softCurrency;
        $output['updateCurrencies'] = CursorsToArray($updateCurrencies);
    }
    echo json_encode($output);
};

if (!\Base::instance()->get('enable_action_request_query')) {
    $f3->route('POST /google-rewarded-ad', function($f3, $params) {
        DoPostAction('google-rewarded-ad', $f3, $params);
    });
}

?>
