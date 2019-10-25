<?php

require_once(__DIR__ . '/Curl.php');

class AmoCrm
{
    private $subDomain;
    private $curl;
    private $user;

    public function __construct($user, $subDomain)
    {
        $this->subDomain = $subDomain;
        $this->user = $user;
        $this->curl = new Curl();
        $this->auth();
    }

    /* Добавить сделку */

    private function auth()
    {
        $link = $link = 'https://' . $this->subDomain . '.amocrm.ru/private/api/auth.php?type=json';
        $out = $this->curl->post($link, $this->user);
        $this->errors($out['code']);
        return json_decode($out['data'], true);
    }

    /* Список сделок */

    private function errors($code)
    {
        $code = (int)$code;
        $errors = array(
            301 => 'Moved permanently',
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        );
        try {
            if ($code != 200 && $code != 204) {
                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
            }

        } catch (Exception $E) {
            die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
        }
    }

    /* Добавить задачи к сделкам без задач */

    public function addLeads($data)
    {
        $link = 'https://' . $this->subDomain . '.amocrm.ru/api/v2/leads';
        $out = $this->curl->post($link, $data);
        $this->errors($out['code']);
        return json_decode($out['data'], true);

    }

    /* Авторизация */

    public function listLeads($params)
    {
        $link = 'https://' . $this->subDomain . '.amocrm.ru/api/v2/leads?' . $params;
        $out = $this->curl->get($link);
        $this->errors($out['code']);
        return json_decode($out['data'], true);
    }

    /* Ошибки */

    public function addTaskLeadsWithoutTask($data)
    {
        $link = 'https://' . $this->subDomain . '.amocrm.ru/api/v2/tasks';
        $out = $this->curl->post($link, $data);
        $this->errors($out['code']);
        return json_decode($out['data'], true);
    }
}