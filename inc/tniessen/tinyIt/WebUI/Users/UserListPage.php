<?php
namespace tniessen\tinyIt\WebUI\Users;

use tniessen\tinyIt\Application;
use tniessen\tinyIt\WebUI\Page;

class UserListPage extends Page
{
    private $page;
    private $users;
    private $hasNextPage;
    private $hasPreviousPage;

    private $errorMessage;


    public function init($params)
    {
        self::requireLogin();

        $dbc = Application::dbConnection();

        $perPage = 20;
        $offset = ($this->page - 1) * $perPage;

        $all = $dbc->users()->getUsers($offset, $perPage + 1);
        $this->hasNextPage = count($all) > $perPage;
        $this->hasPreviousPage = $this->page > 1;
        $this->users = array_slice($all, 0, $perPage);
    }

    public function render()
    {
        $opts = array(
            'users' => $this->users,
            'page' => $this->page,
            'hasNextPage' => $this->hasNextPage,
            'hasPreviousPage' => $this->hasPreviousPage
        );
        if($this->errorMessage !== null) {
            $opts['errorMessage'] = $this->errorMessage;
        }
        $this->renderTemplate('users/userlist', $opts);
    }

    public static function getInstance($path, $params)
    {
        $page = parent::getInstance($path, $params);
        $page->page = isset($params['offset']) ? intval($params['offset']) : 1;
        if($page->page <= 0) $page->page = 1;
        return $page;
    }
}
