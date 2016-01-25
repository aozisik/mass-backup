<?php

// path to composer autoloader
require 'vendor/autoload.php';

use BackupManager\Config\Config;
use BackupManager\Filesystems;
use BackupManager\Databases;
use BackupManager\Compressors;
use BackupManager\Manager;

// build providers
$filesystems = new Filesystems\FilesystemProvider(Config::fromPhpFile('config/storage.php'));
$filesystems->add(new Filesystems\Awss3Filesystem);
$filesystems->add(new Filesystems\LocalFilesystem);

$databases = new Databases\DatabaseProvider(Config::fromPhpFile('config/database.php'));
$databases->add(new Databases\MysqlDatabase);

$compressors = new Compressors\CompressorProvider;
$compressors->add(new Compressors\GzipCompressor);
$compressors->add(new Compressors\NullCompressor);

// build manager
return new Manager($filesystems, $databases, $compressors);