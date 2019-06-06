<?php
class File 
{
    private $conn;
    private $table_name = "files";

    public $id;
    public $filename;
    public $filesize;
    public $filetype;
    public $fileaccess;
    public $author;

    public function __construct($db)
    {
        $this->conn = $db;
    } 
}