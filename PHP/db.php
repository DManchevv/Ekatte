<?php
    class Database {
        private $conn;
        private $insertArea;
        private $insertMunicipality;
        private $insertTownHall;
        private $insertSettlement;

        public function __construct() {
            $config = parse_ini_file('config.ini', true);
            $type = $config['db']['type'];
            $port = $config['db']['port'];
            $host = $config['db']['host'];
            $name = $config['db']['name'];
            $user = $config['db']['user'];
            $password = $config['db']['password'];

            $this->init($type, $port, $host, $name, $user, $password);
        }

        private function init($type, $port, $host, $name, $user, $password) {
            try {
                $this->conn = new PDO("$type:host=$host;port=$port;dbname=$name", $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                $this->prepareStatements();
                echo "Connection successful\n";
            } catch(Exception $e) {
                echo "Connection failed\n" . $e->getMessage();
            }
        }

        public function prepareStatements() {
            $sql = "INSERT INTO public.area (area, name) VALUES (:area, :name)";
            $this->insertArea = $this->conn->prepare($sql);

            $sql = "INSERT INTO public.municipality (municipality, name, area_ID) VALUES (:municipality, :name, :area_ID)";
            $this->insertMunicipality = $this->conn->prepare($sql);

            $sql = "INSERT INTO public.\"town hall\" (town_hall, name, municipality_ID) VALUES (:town_hall, :name, :municipality_ID)";
            $this->insertTownHall = $this->conn->prepare($sql);

            $sql = "INSERT INTO public.settlement (ekatte, t_v_m, name, town_hall_ID) VALUES (:ekatte, :t_v_m, :name, :town_hall_ID)";
            $this->insertSettlement = $this->conn->prepare($sql);

           // $sql = "SELECT * FROM google_maps_points WHERE pointId = :pointId";
           // $this->pointWithId = $this->connection->prepare($sql);

           // $sql = "SELECT x, y FROM google_maps_points";
           // $this->getPoints = $this->connection->prepare($sql);
        }

        public function addSettlement($data) {
            try {
                $this->insertSettlement->execute($data);
                return["success" => true];
            } catch(Exception $e) {
                echo "Connection failed\n s" . $e->getMessage(); 
                return ["success" => false];
            }
        }

        public function addTownHall($data) {
            try {
                $this->insertTownHall->execute($data);
                return["success" => true];
            } catch(Exception $e) {
                echo "Connection failed\n" . $e->getMessage(); 
                return ["success" => false];
            }
        }

        public function addMunicipality($data) {
            try {
                $this->insertMunicipality->execute($data);
                return["success" => true];
            } catch(Exception $e) {
                echo "Connection failed\n" . $e->getMessage(); 
                return ["success" => false];
            }
        }

        public function addArea($data) {
            try {
                $this->insertArea->execute($data);
                return["success" => true];
            } catch(Exception $e) {
                echo "Connection failed \n" . $e->getMessage(); 
                return ["success" => false];
            }
        }

    }

?>