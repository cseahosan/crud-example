<?php
namespace App\Student;
use PDO;
use PDOException;

class Student
{
    private $conn = '';
    public $name = '';
    public $isActive = '';
    public $description;
    public $display_position;
    public $category;
    // private $id;

    public function __construct()
    {
        session_start();
        try {

            $this->conn = new PDO ("mysql:host=localhost;dbname=mystudent_db", "root", "");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        }catch(PDOException $e){
            echo 'Error : '.$e->getMessage();
        }
    }

    public function index()
    {
        $query = "SELECT * FROM students";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll();
    }

    public function show($id)
    {
        $query = "SELECT * FROM students where id=".$id;
        $stmt = $this->conn->query($query);
        return $stmt->fetch();
    }

    public function setData(array $data = [])
    {

        if (isset($_SESSION['validation_error'])){
            unset($_SESSION['validation_error']);
        }

        $rules = $this->rules();
        if (array_key_exists('name', $data)){
            if(strlen($data['name']) < $rules['name']){
                $_SESSION['validation_error'][] = 'Name must be at least '.$rules['name'].' character';
            }
            $this->name = $data['name'];
        }else{
            $_SESSION['validation_error'][] = 'Name input is required';
        }

        if (array_key_exists('description', $data)){
            if(strlen($data['description']) < $rules['description']){
                $_SESSION['validation_error'][] = 'Description must be at least '.$rules['description'].' character';
            }else{
                $this->description = $data['description'];
            }
        }else{
            $_SESSION['validation_error'][] = 'Description input is required';
        }

        if(!is_null($_SESSION['validation_error'])){
            return false;
        }

        return $this;

    }

    public function store()
    {
        try{

            $query ="INSERT INTO students(name,is_active,description,display_position,category) VALUES(:name,:is_active,:description,:checkbox,:category)";
            $stmt =$this->conn->prepare($query);
            $stmt->execute([
                ':name' => $this->name,
                ':is_active' => $this->isActive,
                ':description' => $this->description,
                ':checkbox' => $this->display_position,
                ':category' => $this->category
            ]);

            $_SESSION['message'] = 'Inserted successfully';
            header('location: store.php');

        }catch(PDOException $e){
            $_SESSION['error'] = $e->getMessage();
            header('location: index.php');
        }
    }

    public function update($id)
    { 
        try{

            $query ="UPDATE students set name = :name where id=".$id;
            $stmt =$this->conn->prepare($query);
            $stmt->execute([
                ':name' => $this->name,
            ]);
            $_SESSION['message'] = 'Updated successfully';
            header('location: index.php');

        }catch(PDOException $e){
            $_SESSION['error'] = $e->getMessage();
            header('location: edit.php');
        }
    }

    public function delete($id)
    {
        try{

            $query ="DELETE from students where id=".$id;
            $this->conn->query($query);
            $_SESSION['message'] = 'Deleted successfully';
            header('location: index.php');

        }catch(PDOException $e){
            $_SESSION['error'] = $e->getMessage();
            header('location: index.php');
        }
    }

    public function rules()
    {
        $rules = [
            'name' => 5,
            'description' => 10,
        ];

        return $rules;
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->conn = null;
    }

}