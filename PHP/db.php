<?php
    class Database {
        private $conn;

        private $insertArea;
        private $insertMunicipality;
        private $insertTownHall;
        private $insertSettlement;

        private $selectArea;
        private $selectMunicipality;
        private $selectTownHall;
        private $selectSettlementWithEkatte;
        private $selectSettlementWithName;

        private $deleteArea;
        private $deleteMunicipality;
        private $deleteTownHall;
        private $deleteSettlement;

        private $getSettlements;
        private $getTownHalls;
        private $getMunicipalities;
        private $getAreas;

        private $getSettlementInfo;
        private $getTownHall;
        private $getMunicipality;
        private $getArea;
        
        private $t_v_m;
        private $townHalls;
        private $municipalities;
        private $areas;

        public function __construct() {
            $config = parse_ini_file('config.ini', true);
            $type = $config['db']['type'];
            $port = $config['db']['port'];
            $host = $config['db']['host'];
            $name = $config['db']['name'];
            $user = $config['db']['user'];
            $password = $config['db']['password'];
            $this->t_v_m = array();
            $this->townHalls = array();
            $this->municipalities = array();
            $this->areas = array();

            $this->init($type, $port, $host, $name, $user, $password);
        }

        private function init($type, $port, $host, $name, $user, $password) {
            try {
                $this->conn = new PDO("$type:host=$host;port=$port;dbname=$name", $user, $password,  array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                $this->prepareStatements();
            } catch(PDOException $e) {
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

            $sql = "DELETE FROM public.area";
            $this->deleteArea = $this->conn->prepare($sql);

            $sql = "DELETE FROM public.municipality";
            $this->deleteMunicipality = $this->conn->prepare($sql);

            $sql = "DELETE FROM public.\"town hall\"";
            $this->deleteTownHall = $this->conn->prepare($sql);

            $sql = "DELETE FROM public.settlement";
            $this->deleteSettlement = $this->conn->prepare($sql);

            $sql = "SELECT * FROM public.area WHERE area = :area";
            $this->selectArea = $this->conn->prepare($sql);

            $sql = "SELECT * FROM public.municipality WHERE municipality = :municipality";
            $this->selectMunicipality = $this->conn->prepare($sql);

            $sql = "SELECT * FROM public.\"town hall\" WHERE town_hall = :town_hall";
            $this->selectTownHall = $this->conn->prepare($sql);

            $sql = "SELECT * FROM public.settlement WHERE ekatte = :ekatte";
            $this->selectSettlementWithEkatte = $this->conn->prepare($sql);

            $sql = "SELECT * FROM public.area";
            $this->getAreas = $this->conn->prepare($sql);

            $sql = "SELECT * FROM public.municipality";
            $this->getMunicipalities = $this->conn->prepare($sql);

            $sql = "SELECT * FROM public.\"town hall\"";
            $this->getTownHalls = $this->conn->prepare($sql);

            $sql = "SELECT * FROM public.settlement";
            $this->getSettlements = $this->conn->prepare($sql);

            $sql = "SELECT DISTINCT name FROM public.settlement WHERE UPPER(name) LIKE UPPER(:name) LIMIT 10";
            $this->selectSettlementWithName = $this->conn->prepare($sql);

            $sql = "SELECT t_v_m, town_hall_id FROM public.settlement WHERE UPPER(name) = UPPER(:name) LIMIT 10";
            $this->getSettlementInfo = $this->conn->prepare($sql);

            $sql = "SELECT T.name, T.municipality_id
                    FROM public.settlement S INNER JOIN public.\"town hall\" T
                    ON S.town_hall_id = T.town_hall
                    WHERE S.town_hall_id = :town_hall_id";

            $this->getTownHall = $this->conn->prepare($sql);

            $sql = "SELECT M.name, M.area_id
                    FROM public.\"town hall\" T INNER JOIN public.municipality M
                    ON T.municipality_id = M.municipality
                    WHERE T.municipality_id = :municipality_id";
                    
            $this->getMunicipality = $this->conn->prepare($sql);

            $sql = "SELECT A.name 
                    FROM public.municipality M INNER JOIN public.area A
                    ON M.area_id = A.area
                    WHERE M.area_id = :area_id";

            $this->getArea = $this->conn->prepare($sql);

           // $sql = "SELECT * FROM google_maps_points WHERE pointId = :pointId";
           // $this->pointWithId = $this->connection->prepare($sql);

           // $sql = "SELECT x, y FROM google_maps_points";
           // $this->getPoints = $this->connection->prepare($sql);
        }

        public function getSettlementsNumber() {
            try {
                $this->getSettlements->execute();

                return $this->getSettlements->rowCount();
            } catch (PDOException $e) {
                return "-1";
            }
        }
        
        public function getTownHallsNumber() {
            try {
                $this->getTownHalls->execute();

                return $this->getTownHalls->rowCount();
            } catch (PDOException $e) {
                return "-1";
            }
        }

        public function getMunicipalitiesNumber() {
            try {
                $this->getMunicipalities->execute();

                return $this->getMunicipalities->rowCount();
            } catch (PDOException $e) {
                return "-1";
            }
        }

        public function getAreasNumber() {
            try {
                $this->getAreas->execute();

                return $this->getAreas->rowCount();
            } catch (PDOException $e) {
                return "-1";
            }
        }

        public function findRecord($type, $data, $column) {
            try {
                $input = [
                    $column => $data[$column]
                ];

                try {
                    $type->execute($input);

                    if($type->rowCount() == 0) {
                        return ["success" => false];
                    }

                    return ["success" => true];
                } catch(PDOException $e) {
                    return ["success" => false];
                }

                return ["success" => true];
            } catch(PDOException $e) {
                return ["success" => false];
            }
        }

        public function addSettlement($data) {
            try {
                $foundSettlement = $this->findRecord($this->selectSettlementWithEkatte, $data, "ekatte");
                $foundTownHallReference = $this->findRecord($this->selectTownHall, $data, "town_hall_ID");

                if (!$foundSettlement["success"]) {
                    if (!$foundTownHallReference["success"]) {
                        $townHallData = [
                            "town_hall" => $data["town_hall_ID"],
                            "name" => $data["name"],
                            "municipality_ID" => substr($data["town_hall_ID"], 0, -3)
                        ];

                        $this->addTownHall($townHallData);
                    }

                    $this->insertSettlement->execute($data);
                    return["success" => true];
                }

                return["success" => false];
            } catch(PDOException $e) {
                echo "Connection failed\n" . $e->getMessage(); 
                return ["success" => false];
            }
        }

        public function addTownHall($data) {
            try {
                $foundTownHall = $this->findRecord($this->selectTownHall, $data, "town_hall");

                if (!$foundTownHall["success"]) {
                    $this->insertTownHall->execute($data);
                    return["success" => true];
                }
                
                return["success" => false];
            } catch(PDOException $e) {
                $asd = $data["name"];
                echo "Connection failed $asd\n" . $e->getMessage(); 
                return ["success" => false];
            }
        }

        public function addMunicipality($data) {
            try {
                $foundMunicipality = $this->findRecord($this->selectMunicipality, $data, "municipality");

                if (!$foundMunicipality["success"]) {
                    $this->insertMunicipality->execute($data);
                    return["success" => true];
                }

                return["success" => false];
            } catch(PDOException $e) {
                echo "Connection failed\n" . $e->getMessage(); 
                return ["success" => false];
            }
        }

        public function addArea($data) {
            try {
                $foundArea = $this->findRecord($this->selectArea, $data, "area");
                
                if (!$foundArea["success"]) {
                    $this->insertArea->execute($data);
                    return["success" => true];
                }

                return["success" => false];
            } catch(PDOException $e) {
                echo "Connection failed \n" . $e->getMessage(); 
                return ["success" => false];
            }
        }

        public function getSettlementsWithName($data) {
            try {
                $this->selectSettlementWithName->execute($data);
                return["success" => true, "data" => $this->selectSettlementWithName->fetchAll(PDO::FETCH_ASSOC)];
            } catch (PDOException $e) {
                return ["success" => false];
            }
        }

        public function getSettlementInfo($data) {
            try {
                $this->getSettlementInfo->execute($data);
                
                while ($row = $this->getSettlementInfo->fetch(PDO::FETCH_ASSOC)) {
                    $this->getTownHallInfo(["town_hall_id" => $row["town_hall_id"]]);
                
                    if (!in_array($row["t_v_m"], $this->t_v_m)) {
                        array_push($this->t_v_m, $row["t_v_m"]);
                    }
                }

                return ["t_v_m" => $this->t_v_m, "townHall" => $this->townHalls, "municipality" => $this->municipalities, "area" => $this->areas];

            } catch (PDOException $e) {
                return ["success" => false];
            }
        }

        public function getTownHallInfo($data) {
            try {
                $this->getTownHall->execute($data);

                while ($row = $this->getTownHall->fetch(PDO::FETCH_ASSOC)) {
                    $this->getMunicipalityInfo(["municipality_id" => $row["municipality_id"]]);

                    if (!in_array($row["name"], $this->townHalls)) {
                        array_push($this->townHalls, $row["name"]);
                    }
                    
                }
            } catch(PDOException $e) {
                return [];
            }
        }

        public function getMunicipalityInfo($data) {
            try {
                $this->getMunicipality->execute($data);

                while ($row = $this->getMunicipality->fetch(PDO::FETCH_ASSOC)) {
                    $this->getAreaInfo(["area_id" => $row["area_id"]]);
                    if (!in_array($row["name"], $this->municipalities)) {
                        array_push($this->municipalities, $row["name"]);
                    }
                }


            } catch(PDOException $e) {
                return [];
            }
        }

        public function getAreaInfo($data) {
            try {
                $this->getArea->execute($data);

                while ($row = $this->getArea->fetch(PDO::FETCH_ASSOC)) {
                    if (!in_array($row["name"], $this->areas)) {
                        array_push($this->areas, $row["name"]);
                    }
                }
                
            } catch(PDOException $e) {
                return [];
            }
        }
    }

?>