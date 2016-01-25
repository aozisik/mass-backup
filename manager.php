<?php

require 'bootstrap.php';

use BackupManager\Filesystems\FilesystemProvider;
use BackupManager\Filesystems\Awss3Filesystem;
use BackupManager\Filesystems\LocalFilesystem;

use BackupManager\Databases\DatabaseProvider;
use BackupManager\Databases\MysqlDatabase;

use BackupManager\Config\Config;
use BackupManager\Manager;

use BackupManager\Compressors\CompressorProvider;
use BackupManager\Compressors\GzipCompressor;
use BackupManager\Compressors\NullCompressor;

class DatabaseBackups
{
    private $databases = [];
    private $compressors;

    public function __construct()
    {
        $this->compressors = new CompressorProvider;
        $this->compressors->add(new GzipCompressor);
        $this->compressors->add(new NullCompressor);
    }

    public function addDatabase($database)
    {
        $this->databases[] = $database;
        return $this;
    }

    public function backupAllTo($target)
    {
        foreach ($this->databases as $database) {
            $this->backupTo($database, $target);
        }
    }

    public function backupTo($database, $target)
    {
        $this->getManagerForDatabase($database, $target)->makeBackup()->run('target', $target, 'backups', 'gzip');
    }

    protected function getManagerForDatabase($database, $target)
    {
        $config = require('config/database.php');
        $config['target']['database'] = $database;

        $databases = new DatabaseProvider(new Config($config));
        $databases->add(new MysqlDatabase);

        $fileConfig = require('config/storage.php');
        $fileConfig[$target]['root'] .= $database . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '_' . uniqid();

        $filesystem = new FilesystemProvider(new Config($fileConfig));
        $filesystem->add(new LocalFilesystem);
        $filesystem->add(new Awss3Filesystem);

        return new Manager($filesystem, $databases, $this->compressors);
    }
}