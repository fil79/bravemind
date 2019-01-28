<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
//04-2015
//FMOREIRA
// CSV CLASS
/*
 * SET DB CONNECTION
 * CREATE TABLES
 * IMPORTC CSV
 * INSERT CLIENT
 * INSERT DEALS
 * INSERT SALES
 * READ SALES
 * GET SALES_ID
 */

class Csv
{

    protected $conn;
    protected $file;
    protected $host;
    protected $username;
    protected $password;
    protected $db;

    public function __construct()
    {

        //CONNECT TO MYSQL SERVER INSERT CREDIANTIALS OF A EXISTING USER

        $this->host     = "localhost";
        $this->username = "root";
        $this->password = "password";
        $this->db       = "test";

    }

    public function createdb()
    {

        try {
            $dbh = new PDO("mysql:host=$this->host", $this->username, $this->password);

            $dbh->exec("CREATE DATABASE `$this->db`;
		            GRANT ALL ON `$this->db`.* TO '$this->username'@'localhost';
		            FLUSH PRIVILEGES;");

        } catch (PDOException $e) {
            die("DB ERROR: " . $e->getMessage());
        }
    }

    public function setdb()
    {

        // connection parameters
        $dsn = 'mysql:dbname=' . $this->db . ';host=' . $this->host . ';charset=utf8';

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password);
            return array("error" => 0);

        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }

    }

    public function createTables()
    {

        //CREATE TABLE CLIENT
        $sql_client = "

				CREATE TABLE IF NOT EXISTS `client` (
				  `client` varchar(100),
				   `id` int(9) NOT NULL
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

                 ";

        $st_client = $this->conn->prepare($sql_client);
        //CREATE TABLE DEAL
        $sql_deal = "

				CREATE TABLE IF NOT EXISTS `deal` (
 				  `deal` varchar(100),
				  `id` int(9) NOT NULL
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

                 ";

        $st_deal = $this->conn->prepare($sql_deal);

        //CREATE TABLE SALES
        $sql_sales = "

				CREATE TABLE IF NOT EXISTS `sales` (

				  `deal_id` int(9) NOT NULL,
				  `hour` datetime,
				  `sent` int(3),
				  `accepted` int(3),
				  `refused` int(3)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

                 ";
        $st_sales = $this->conn->prepare($sql_sales);

        try {
            $st_client->execute();
            $st_deal->execute();
            $st_sales->execute();
            return array("error" => 0);
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

    }

    public function importCsv($csv)
    {
        $row = 1;
        if (($handle = fopen($csv, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $num = count($data);
                echo ".";

                if ($row != 1) {

                    $client       = explode("@", $data[0]);
                    $name         = $client[0];
                    $id           = $client[1];
                    $users_exists = $this->checkClient($id);
                    if (!$users_exists) {
                        $arrClient = array('id' => $id, 'client' => $name);
                        $this->insertClient($arrClient);
                    }
                    $deal        = explode("#", $data[1]);
                    $deal_name   = $deal[0];
                    $deal_id     = $deal[1];
                    $deal_exists = $this->checkDeal($deal_id);
                    if (!$deal_exists) {

                        $arrDeal = array('id' => $deal_id, 'deal' => $deal_name);
                        $this->insertDeal($arrDeal);
                    }
                    $date = date("Y-m-d H:i:s", strtotime($data[2]));

                    $arrSales = array('deal_id' => $deal_id, 'hour' => $date, 'sent' => $data[3], 'accepted' => $data[4], 'refused' => $data[5]);
                    $this->insertSales($arrSales);

                }
                $row++;
            }
            fclose($handle);
        }
        echo "Total rows imported: " . (int) ($row - 1);
    }

    public function checkClient($client)
    {
//NO REPEATED CLIENTS IN CLIENT TABLE
        //CHECK CLIENT
        $sql_client = "

						SELECT* from client where id=" . $client;

        $st_client = $this->conn->prepare($sql_client);

        try {
            $st_client->execute();
            $count = $st_client->rowCount();
            if ($count > 0) {
                return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

    }

    public function checkDeal($deal)
    {
//NO REPEATED DEALS IN CLIENT TABLE
        //CHECK DEAL
        $sql_deal = "

						SELECT* from deal where id=" . $deal;

        $st_deal = $this->conn->prepare($sql_deal);

        try {
            $st_deal->execute();
            $count = $st_deal->rowCount();
            if ($count > 0) {
                return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

    }

    public function insertClient($arrClient)
    {
        //INSERT CLIENT
        $sql_client = "

						INSERT INTO client (id,client)
						VALUES('" . $arrClient['id'] . "', '" . $arrClient['client'] . "')

                 	";
        $st_client = $this->conn->prepare($sql_client);

        try {
            $st_client->execute();
            return array("error" => 0);
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function insertDeal($arrDeal)
    {
        //INSERT Deal
        $sql_deal = "

						INSERT INTO deal (id,deal)
						VALUES('" . $arrDeal['id'] . "', '" . $arrDeal['deal'] . "')

                 	";
        $st_deal = $this->conn->prepare($sql_deal);

        try {
            $st_deal->execute();
            return array("error" => 0);
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function insertSales($arrSales)
    {
        //INSERT SALES
        $sql_sales = "

						INSERT INTO sales (deal_id,hour,sent,accepted,refused)
						VALUES('" . $arrSales['deal_id'] . "', '" . $arrSales['hour'] . "', '" . $arrSales['sent'] . "', '" . $arrSales['accepted'] . "', '" . $arrSales['refused'] . "')

                 	";
        $st_sales = $this->conn->prepare($sql_sales);

        try {
            $st_sales->execute();
            return array("error" => 0);

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function getSales($groupBy, $id)
    {
        $sum = "*";
        if ($groupBy) {
            $group = " GROUP BY " . $groupBy . "(hour)";
            $sum   = " SUM(sent) as sent ,SUM(accepted) as accepted ,SUM(refused) as refused ";
        } else {
            $group = null;
        }

        if (!empty($id)) {

            $id = " WHERE deal_id=" . $id;
        } else {
            $id = null;
        }

        //SELECT SALES
        $sql_sales = "
        				SELECT " . $sum . " from  sales " . $id . "  " . $group . "
                 	";

        echo $sql_sales;
        $st_sales = $this->conn->prepare($sql_sales);

        try {
            $st_sales->execute();
            $sales = $st_sales->fetchAll();

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }

        return $sales;
    }

    public function getSalesId()
    {
        //SELECT SALES
        $sql_sales = "

						SELECT deal_id from  sales group by deal_id

                 	";
        $st_sales = $this->conn->prepare($sql_sales);

        try {
            $st_sales->execute();
            $sales = $st_sales->fetchAll();

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        return $sales;

    }
} //END OF CLASS
