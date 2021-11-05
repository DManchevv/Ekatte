<?php
    require_once 'db.php';
    header('Content-Type: application/json');

    class Statistics {
        public function __construct() {
            $this->db = new Database();
        }

        public function getStatistics() {
            $data = [
                "settlements" => $this->db->getSettlementsNumber(),
                "town_halls" => $this->db->getTownHallsNumber(),
                "municipalities" => $this->db->getMunicipalitiesNumber(),
                "areas" => $this->db->getAreasNumber()
            ];

            $data["success"] = true;
            
            echo json_encode($data);
        }

    }

    $statistics = new Statistics();
    $statistics->getStatistics();

?>