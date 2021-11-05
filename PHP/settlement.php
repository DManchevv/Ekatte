<?php
    require_once 'db.php';
    header('Content-Type: application/json');

    class Settlement {
        private $ekatte;
        private $t_v_m;
        private $name;
        private $area;
        private $municipality;
        private $townHall;

        public function __construct() {
            $this->db = new Database();
        }

        public function addSettlement($ekatte, $t_v_m, $name, $town_hall_ID) {
            $data = [
                "ekatte" => $ekatte,
                "t_v_m" => $t_v_m,
                "name" => $name,
                "town_hall_ID" => $town_hall_ID
            ];

            $query = $this->db->addSettlement($data);
        }

        public function addTownHall($townHall, $name, $municipalityID) {
            $data = [
                "town_hall" => $townHall,
                "name" => $name,
                "municipality_ID" => $municipalityID
            ];

            $query = $this->db->addTownHall($data);
        }

        public function addMunicipality($municipality, $name, $area_ID) {
            $data = [
                "municipality" => $municipality,
                "name" => $name,
                "area_ID" => $area_ID
            ];

            $query = $this->db->addMunicipality($data);
        }

        public function addArea($area, $name) {
            $data = [
                "area" => $area,
                "name" => $name
            ];

            $query = $this->db->addArea($data);
        }

        public function readSettlementCSV() {
            $row = 0;
            if (($handle = fopen("../csv/Ek_atte.csv", "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($row < 2) {
                        $row++;
                    }
                    else {
                        $this->addSettlement($data[0], $data[1], $data[2], $data[5]);
                    }
                }
                fclose($handle);
            }
        }

        public function readTownHallCSV() {
            $isFirstRow = true;
            if (($handle = fopen("../csv/Ek_kmet.csv", "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($isFirstRow) {
                        $isFirstRow = false;
                    }
                    else {
                        $municipalityID = substr($data[0], 0, -3);
                        $this->addTownHall($data[0], $data[1], $municipalityID);
                    }
                }
                fclose($handle);
            }

            $this->readSettlementCSV();
        }

        public function readMunicipalityCSV() {
            $isFirstRow = true;
            if (($handle = fopen("../csv/Ek_obst.csv", "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($isFirstRow) {
                        $isFirstRow = false;
                    }
                    else {
                        $areaID = substr($data[0], 0, -2);
                        $this->addMunicipality($data[0], $data[2], $areaID);
                    }
                }
                fclose($handle);
            }

            $this->readTownHallCSV();
        }

        public function readAreaCSV() {
            $isFirstRow = true;
            if (($handle = fopen("../csv/Ek_obl.csv", "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($isFirstRow) {
                        $isFirstRow = false;
                    }
                    else {
                        $this->addArea($data[0], $data[2]);
                    }
                }
                fclose($handle);
            }

            $this->readMunicipalityCSV();
        }
    }

    $settlement = new Settlement();

    //$settlement->addMunicipality("SFL02", "test", "SOF");
    //$settlement->addArea("SOF", "Sofia");
    $settlement->readAreaCSV();
    //$settlement->readMunicipalityCSV();
    //$settlement->readTownHallCSV();
    //$settlement->readSettlementCSV();

?>