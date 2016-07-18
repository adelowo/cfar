<?php
namespace adelowo\controller;

class HomeController
{

    public function showUser($id, $param)
    {
        //do something with $id and $param;
    }

    public function showPdf($name)
    {

        echo $name;
    }

    public function indexAction($id, $name)
    {
        echo $id . PHP_EOL;
        echo $name;
    }
}
