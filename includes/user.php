<?php 

//Klassen som omgjør alt ved håndtering av brukere i databasen.
class User extends Db_object{

	//Klasse-variabler kalles properties.
	protected static $db_table = "users"; //Slik at man kan endre navnet på databasetabellen.

	//Array skal brukes i properies() og inneholder bruker-variablene til objektet.
	protected static $db_table_fields = array('username', 'email', 'password', 'first_name', 'middle_name', 'last_name', 'user_image', 'joined');
	public $id;
	public $username;
	public $email;
	public $password;
	public $first_name;
	public $middle_name;
	public $last_name;
	public $user_image;
	public $joined;

	// Verifiserer at brukeren ligger i databasen, brukes ved login og kan brukes andre steder.
	public static function verify_user($username, $password) {

		global $database;
		$username = $database->escape_string($username);
		$password = $database->escape_string($password);

		$sql = "SELECT * FROM " . self::$db_table . " WHERE ";
		$sql .= "username = '{$username}' ";
		$sql .= "AND password = '{$password}' ";
		$sql .= "LIMIT 1";


		/* ----------------------------- HASHED PASSORD SATT PÅ VENT TIL NÅ ------------------------------*/
		$the_result_array = self::find_by_query($sql);

		//$hashed_password = $the_result_array->password; 
		//$password = password_verify($password, $hashed_password);
		
		if (!empty($the_result_array)) {

			// Array shif delivers the first
			return  array_shift($the_result_array);
		} else {

			return false;
		}


		//return !empty($the_result_array) ? array_shift($the_result_array) : false; // ternery syntax.
	}

	// Collects the placement of the game path, used when showing the picture at  the list of users.
	public function get_user_image() {

		return "img" . DS . "profile" . DS . "default" . DS . $this->user_image;
	
	}

	// Verifiserer at brukeren ligger i databasen, brukes ved llogin og kan brukes andre steder.
	// Kan kuttes opp senere.
	public static function verify_new_user($username, $email, $password, $password_check, $first_name, $middle_name, $last_name) {

		/*
		 * Legger feilmeldinger inn i error_array, error arrayet sendes så tilbake. 
		 * Hvis det er felmeldinger vil ikke brukeren bli laget, og feilmeldingene vil vises.
		*/

		global $database;

		//Creates the error array, error messages will be pushed into this, and 
		$error_array       = array();
		$username          = $database->escape_string($username);
		$email             = $database->escape_string($email);
		$password          = $database->escape_string($password);
		$password_check    = $database->escape_string($password_check);
		$first_name        = $database->escape_string($first_name);
		$middle_name       = $database->escape_string($middle_name);
		$last_name         = $database->escape_string($last_name);


		// Fjerner all potensiel sql kode.
		$username          = strip_tags($username);
		$email             = strip_tags($email);
		$first_name        = strip_tags($first_name);
		$middle_name       = strip_tags($middle_name);
		$last_name         = strip_tags($last_name);
		$password          = strip_tags($password);
		$password_check    = strip_tags($password_check);

		//Sjekker om brukernavn eller email ligger i databasen.
		$sql  = "SELECT * FROM " . self::$db_table . " WHERE ";
		$sql .= "username = '{$username}' ";
		$sql .= "LIMIT 1";
		$the_result_array = self::find_by_query($sql);
		if (!empty($the_result_array)) {
			array_push($error_array, "The username is already in use, pick something else!");
		}

		//SJEKKER EMAIL:
		if(filter_var($email, FILTER_VALIDATE_EMAIL)) { 

			$email = filter_var($email, FILTER_VALIDATE_EMAIL); 
			$email_check = $database->query("SELECT email FROM " . self::$db_table . " WHERE email='$email'");
			$num_rows = mysqli_num_rows($email_check); 

			/*Error message: The email is in the database.*/
			if($num_rows > 0) {  
				array_push($error_array, "Email already in use");
			}

		} else {
			array_push($error_array, "Invalid email format"); 
		}

		/*Error message: If the passwords are not the same*/
		if($password != $password_check) { 
			array_push($error_array,  "Your passwords do not match");
		}

		/*Error message: If the passwords contain other than numbers and letters.*/
		if(preg_match('/[^A-Za-z0-9]/', $password)) {  
			array_push($error_array, "Your password can only contain english characters or numbers");
		}
		
		/*Error message: If the password is not between 5 and 30 characters long.*/
		if((strlen($password) > 30) || strlen($password) < 5) {  
			array_push($error_array, "Your password must be between 5 and 30 characters");
		}


		//Check password - bruker check_password til å sjekke om et passord fungerer, kommer til å tilkalle verify_password.
		if (empty($error_array)) {

			//Check username - bruker check_username til å sjekke om brukernavnet fungerer.

			//sets up the new user and creates it with create();
			$user = new user();

			/*------------------------------------ Skal brukes når hashet passord settes opp --------------------------------------------*/
			//$password = password_hash($password, PASSWORD_BCRYPT);

			$user->username    = $username;
			$user->email       = $email;
			$user->password    = $password;
			$user->first_name  = $first_name;
			$user->middle_name = $middle_name;
			$user->last_name   = $last_name;
			$user->user_image  = "1.png";
			$user->joined      = date("Y-m-d");

			$user->create();

			return $error_array; // ternery syntax.

		} else {

			return $error_array;

		}
	}


}

?>