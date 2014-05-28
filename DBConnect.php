<?php
	class DBConnect {
		protected static $DBHost = "";
		protected static $DBUser = "";
		protected static $DBPass = "";
		protected static $db;
		
		// Constructor method that sets all necessary variables 
		// without the need for user getters/setters
		function __construct($nDBHost, $nDBUser, $nDBPass) {
	    	self::$DBHost = $nDBHost;
	    	self::$DBUser = $nDBUser;
	    	self::$DBPass = $nDBPass;
	    	return $this->getConnection();
	    }

		// Connection method that connects to the database
		private function Connection(){
			$conn = NULL;
			if(self::$DBUser == "" || self::$DBPass == "" || self::$DBHost == "") {
				echo "Please supply the necessary database connection information.";
				return false;
			}

			try{
            	$conn = new PDO(self::$DBHost, self::$DBUser, self::$DBPass);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch(PDOException $e){
                echo 'ERROR: ' . $e->getMessage();
            }    
            $this->db = $conn;
		}
		
		// Connect to an instance of the DB using this method.
		// If an instance already exists, we use that one, if
		// not, we create a new one and use it.
		public function getConnection(){
			if (self::$db) {
				return self::$db;
			} else {
				$this->Connection();
				return self::$db;
			}
	    }
		
		// Function that will create all the necessary tables on a db for crawling
		private function createTables($DBhostname, $DBpass) {
			
		}
	}
	
	// Tests
	$db = new DBConnect("mysql:host=localhost;dbname=database", "root", "root");
	
?>