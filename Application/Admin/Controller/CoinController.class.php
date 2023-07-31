<?php

namespace Admin\Controller;

class CoinController extends AdminController
{
    private $Model;

    public function __construct()
    {
        parent::__construct();
        $this->Model = M('Coin');
        $this->Title = 'Coin Config';
    }

    public function save()
    {
    }
}

?>