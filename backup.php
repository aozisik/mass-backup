<?php

require 'manager.php';

$manager = new DatabaseBackups();

$manager
    ->addDatabase('database')
    ->backupAllTo('local');