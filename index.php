<?php

require_once(__DIR__ . '/class/AmoCrm.php');
$user = include(__DIR__ . '/config/config.php');
$amoCrm = new AmoCrm($user['auth'], $user['subdomains']);

/* Добавить сделки без задач */
//$leads['add'] = [
//    [
//        'name' => 'Бумага',
//        'created_at' => time(),
//        'status_id' => 7087609,
//        'sale' => 600200,
//        'responsible_user_id' => 1,
//    ],
//    [
//        'name' => 'Карандаши',
//        'created_at' => time(),
//        'status_id' => 7087609,
//        'sale' => 600200,
//        'responsible_user_id' => 1,
//    ],
//];
//$result = $amoCrm->addLeads($leads);

/* Находим сделки без задач и ставим им задачу */
$params = 'filter[tasks]=1';
$leads = $amoCrm->listLeads($params);
$tasks = [];
if (is_array($leads['_embedded']['items'])) {
    foreach ($leads['_embedded']['items'] as $lead) {
        $tasks['add'][] = [
            'element_id' => $lead['id'],
            'element_type' => 2,
            'task_type' => 1,
            'text' => 'Сделка без задачи',
            'responsible_user_id' => 1,
            'complete_till_at' => time() + 3600 * 24 * 2,
        ];
    }
}
if (!empty($tasks)) {
    $result = $amoCrm->addTaskLeadsWithoutTask($tasks);
}