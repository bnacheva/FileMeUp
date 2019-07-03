<?php
class File 
{
    private $conn;
    private $table_name = "files";

    public $id;
    public $email;
    public $file_name;
    public $access;

    public function __construct($db)
    {
        $this->conn = $db;
    } 
    
    function add()
    {
        $query = "INSERT INTO " . $this->table_name . "
            SET
                email = :email,
                file_name = :file_name,
                access = :access";

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->file_name = htmlspecialchars(strip_tags($this->file_name));
        $this->access = htmlspecialchars(strip_tags($this->access));

        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':file_name', $this->file_name);
        $stmt->bindParam(':access', $this->access);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function show_my_files()
    {
        $query = "SELECT email, file_name
        FROM " . $this->table_name . "
        WHERE email = ?";
        $stmt = $this->conn->prepare($query);

        $email = htmlspecialchars(strip_tags("daniel@abv.bg"));
        $stmt->bindParam(1, $email);
        $stmt->execute();
        return $stmt;
    }

    function show_all_files()
    {
        $query = "SELECT email, file_name
        FROM " . $this->table_name . "
        WHERE access = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function show_file($file_name)
    {
        $query = "SELECT email, file_name
        FROM " . $this->table_name . "
        WHERE email = ?
        LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        $file_name = htmlspecialchars(strip_tags($file_name));

        $stmt->bindParam(1, $file_name);

        $stmt->execute();
    }

    function fileExists($file_name)
    {
        $query = "SELECT id, email, file_name, access
        FROM " . $this->table_name . "
        WHERE file_name = ?
        LIMIT 0,1";
        $stmt = $this->conn->prepare($query);

        $file_name = htmlspecialchars(strip_tags($file_name));

        $stmt->bindParam(1, $file_name);

        $stmt->execute();

        $num = $stmt->rowCount();

        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->file_name = $row['file_name'];
            $this->access = $row['access'];

            return true;
        }

        return false;
    }
}