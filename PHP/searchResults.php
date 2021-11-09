<?php
    require_once 'db.php';

    class SearchResult {
        private $db;
        private $in;
        private $name;

        public function __construct() {
            $this->db = new Database();
            $this->in = file_get_contents('php://input');
            $this->name = json_decode($this->in, true);
            $this->name = $this->name["name"];
        }

        public function getResults() {
            $result = $this->db->getSettlementInfo(["name" => $this->name]);

            echo json_encode($result);
        }
    }

    $result = new SearchResult();
    $result->getResults();
    
?>