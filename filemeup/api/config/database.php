<?php
class Database
{
    private $host = "localhost";
    private $db_name = 'filemeup';
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
            $stmt = $this->conn->query("SELECT COUNT(*) 
                FROM INFORMATION_SCHEMA.SCHEMATA 
                WHERE SCHEMA_NAME = '$this->db_name'");
            $db_exists = $stmt->fetchColumn();
            if (!$db_exists) {
                $res = $this->conn->query("CREATE DATABASE $this->db_name CHARACTER SET utf8 COLLATE utf8_general_ci");
                if ($res) {
                    $this->conn->query("use $this->db_name");
                    $sqlExistsUsers = "DROP TABLE IF EXISTS `users`";
                    $this->conn->query($sqlExistsUsers);
                    $sqlCreateUsers = "CREATE TABLE `users` (
                        `id` int(11) NOT NULL,
                        `email` varchar(255) NOT NULL,
                        `firstname` varchar(100) NOT NULL,
                        `lastname` varchar(100) NOT NULL,
                        `password` varchar(2056) NOT NULL,
                        `role` enum('Admin','User') NOT NULL DEFAULT 'User'
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
                    $res = $this->conn->query($sqlCreateUsers);
                    if ($res) {
                        $sqlInsertIntoUsers = "INSERT INTO `users` 
                        (`id`, `email`, `firstname`, `lastname`, `password`, `role`) VALUES
                        (1, 'bori97@abv.bg', 'Боряна', 'Начева', '$2y$10\$tSuPGdnMLl4/gj9zOY7ISO48PzWlc1kNWFYBKspRUGAxr1P/y88vS', 'Admin')";
                        $this->conn->query($sqlInsertIntoUsers);
                        $sqlAlterUsers = "ALTER TABLE `users`
                        ADD PRIMARY KEY (`id`),
                        ADD UNIQUE KEY `email` (`email`)";
                        $this->conn->query($sqlAlterUsers);
                        $sqlModify = "ALTER TABLE `users`
                        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
                        COMMIT";
                        $this->conn->query($sqlModify);
                    } else {
                        echo "Cannot create users table";
                    }
                    $sqlExistsFiles = "DROP TABLE IF EXISTS `files`";
                    $this->conn->query($sqlExistsFiles);
                    $sqlCreateFiles = "CREATE TABLE `files` (
                        `id` int(11) NOT NULL,
                        `email` varchar(255) NOT NULL,
                        `file_name` varchar(2056) NOT NULL,
                        `access` boolean NOT NULL DEFAULT 1
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
                    $res = $this->conn->query($sqlCreateFiles);
                    if ($res) {
                        $sqlAlterFiles = "ALTER TABLE `files`
                        ADD PRIMARY KEY (`id`,`email`)";
                        $this->conn->query($sqlAlterFiles);
                        $sqlModify = "ALTER TABLE `files`
                        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT";
                        $this->conn->query($sqlModify);
                    } else {
                        echo "Cannot create users table";
                    }
                }
                else {
                    echo "Cannot create database";
                }
            } else {
                $this->conn->query("use $this->db_name");
            }
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}