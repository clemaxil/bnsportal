<?php

class ContactsView extends AppView
{
    function __construct()
    {  

    }


    public function default(array $dataView)
    {
        parent::setView(__DIR__.'/views/default.php');
        echo parent::render($dataView);
    }

}