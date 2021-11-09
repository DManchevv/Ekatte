<?php
    require_once 'db.php';

    class Autocomplete {

        private $name;
        private $db;
        private $in;

        public function __construct() {
            $this->db = new Database();
            $this->in = file_get_contents('php://input');
            $this->name = json_decode($this->in, true);
            $this->name = $this->name["name"];

        }

        public function autocomplete() {
            $data = [
                "name" => $this->name
            ];

            $result = $this->db->getSettlementsWithName($data);
            //echo json_encode($result);

            if ($result["success"]) {
                echo json_encode($result);
            }
        }

    }

    $autocomplete = new Autocomplete();
    $autocomplete->autocomplete();

?>