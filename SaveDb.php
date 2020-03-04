<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/03/04
 * Time: 12:58 PM
 */

namespace Naya;

use Carbon\Carbon;

class SaveDb
{
    private $obj;
    private $conn;

    public function __construct($obj, $db) {
        $this->obj = $obj;
        $this->conn = $db;
    }

    public function save() {
        $result = $this->obj->getResult();

        echo "\t db SAVE".PHP_EOL;

        //$this->testQuery();
    }



    /**
     * test Query
     * https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/data-retrieval-and-manipulation.html#data-retrieval-and-manipulation
    */

    private function testQuery() {
        //$this->tInsert();
        //$this->tUpdate();
        //$this->tDelete();
    }

    private function tUpdate() {
        $data = [
            'title' => 'pdo11',
            'content' => 'pdocontent 11',
         ];
        $this->conn->update('movie', $data, ['idx'=>'39']);
    }

    private function tDelete() {
        $idx = [
            'idx' => '40',
            'idx' => '41',
        ];

        $this->conn->delete('movie', $idx);
    }

    private function tInsert() {
        $ins = [
            'title'=> 'pdo',
            'content'=> 'pdo conent',
            'date'=> Carbon::now(),
        ];
        $this->conn->insert('movie', $ins);
        $id = $this->conn->lastInsertId();
        echo $id.PHP_EOL;
    }

    private function tSelect() {
        $stmt = $this->conn->prepare('SELECT * FROM movie');
        $stmt->execute();
        $movies = $stmt->fetchAll();
        echo '<pre>';
        print_r($movies);
        exit;
    }

    private function tSelectWhere() {
        $title = "11";

        $sql = "select * from movie WHERE title=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $title);
        $stmt->execute();

        while($row = $stmt->fetch()) {
            echo $row['title'].PHP_EOL;
        }
    }

}