<?php
/*
 GaiaEHR (Electronic Health Records)
 User.php
 User dataProvider
 Copyright (C) 2012 Ernesto J. Rodriguez (Certun)

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
if(!isset($_SESSION)){
	session_name('_REGEXEC');
	session_start();
	session_cache_limiter('private');
}
include_once ($_SESSION['root'] . '/models/Person.php');
include_once ($_SESSION['root'] . '/classes/AES.php');
include_once ($_SESSION['root'] . '/classes/dbHelper.php');
class User
{

	/**
	 * @var dbHelper
	 */
	private $db;
	/**
	 * @var
	 */
	private $user_id;

	function __construct()
	{
		$this->db = new dbHelper();
		return;
	}

	/**
	 * @return AES
	 */
	private function getAES()
	{
		return new AES($_SESSION['site']['AESkey']);
	}

	public function getCurrentUserId()
	{
		return $_SESSION['user']['id'];
	}

	public function getCurrentUserTitleLastName()
	{
		$id = $this->getCurrentUserId();
		$this->db->setSQL("SELECT title, lname FROM users WHERE id = '$id'");
		$foo = $this->db->fetchRecord();
		$foo = $foo['title'] . ' ' . $foo['lname'];
		return $foo;
	}

	/**
	 * @param stdClass $params
	 * @return array
	 */
	public function getUsers(stdClass $params)
	{
		$this->db->setSQL("SELECT u.*, r.role_id
                             FROM users AS u
                        LEFT JOIN acl_user_roles AS r ON r.user_id = u.id
                            WHERE u.authorized = 1 OR u.username != ''
                         ORDER BY u.username
                            LIMIT $params->start,$params->limit");
		$rows = array();
		foreach($this->db->fetchRecords(PDO::FETCH_ASSOC) as $row){
			$row['fullname'] = Person::fullname($row['fname'], $row['mname'], $row['lname']);
			unset($row['password'], $row['pwd_history1'], $row['pwd_history2']);
			array_push($rows, $row);
		}
		return $rows;
	}

	public function getUserNameById($id)
	{
		$this->db->setSQL("SELECT title, lname FROM users WHERE id = '$id'");
		$user     = $this->db->fetchRecord();
		$userName = $user['title'] . ' ' . $user['lname'];
		return $userName;
	}

	public function getUserFullNameById($id)
	{
		$this->db->setSQL("SELECT title, fname, mname, lname FROM users WHERE id = '$id'");
		$user     = $this->db->fetchRecord();
		$userName = Person::fullname($user['fname'], $user['mname'], $user['lname']);
		return $userName;
	}

	public function getCurrentUserData()
	{
		$id = $this->getCurrentUserId();
		$this->db->setSQL("SELECT * FROM users WHERE id = '$id'");
		$user = $this->db->fetchRecord();
		return $user;
	}

	public function getCurrentUserBasicData()
	{
		$id = $this->getCurrentUserId();
		$this->db->setSQL("SELECT id, title, fname, mname, lname FROM users WHERE id = '$id'");
		$user = $this->db->fetchRecord();
		return $user;
	}

	/**
	 * @param stdClass $params
	 * @return stdClass
	 */
	public function addUser(stdClass $params)
	{
		if(!$this->usernameExist($params->username)){
			$data = get_object_vars($params);
			unset($data['password']);
			$role['role_id'] = $data['role_id'];
			unset($data['id'], $data['role_id'], $data['fullname']);
			if($data['taxonomy'] == ''){
				unset($data['taxonomy']);
			}
			foreach($data as $key => $val){
				if($val == null || $val == ''){
					unset($data[$key]);
				}
			}
			$sql = $this->db->sqlBind($data, 'users', 'I');
			$this->db->setSQL($sql);
			$this->db->execLog();
			$params->id = $this->user_id = $this->db->lastInsertId;
			$params->fullname = Person::fullname($params->fname, $params->mname, $params->lname);
			if($params->password != ''){
				$this->changePassword($params->password);
			}
			$params->password = '';
			$role['user_id']  = $params->id;
			$sql              = $this->db->sqlBind($role, 'acl_user_roles', 'I');
			$this->db->setSQL($sql);
			$this->db->execLog();
			return $params;
		}else{
			return array('success' => false, 'error' => "Username \"$params->username\" exist, please try a different username");
		}
	}

	/**
	 * @param stdClass $params
	 * @return stdClass
	 */
	public function updateUser(stdClass $params)
	{
		$data             = get_object_vars($params);
		$params->password = '';
		$this->user_id   = $data['id'];
		$role['role_id'] = $data['role_id'];
		unset($data['id'], $data['role_id'], $data['fullname']);
		if($data['password'] != ''){
			$this->changePassword($data['password']);
		}
		unset($data['password']);
		$sql = $this->db->sqlBind($role, 'acl_user_roles', 'U', array('user_id' => $this->user_id));
		$this->db->setSQL($sql);
		$this->db->execLog();
		$sql = $this->db->sqlBind($data, 'users', 'U', array('id' => $this->user_id));
		$this->db->setSQL($sql);
		$this->db->execLog();
		return $params;

	}

	public function usernameExist($username){
		$this->db->setSQL("SELECT count(id) FROM users WHERE username = '$username'");
		$user = $this->db->fetchRecord();
		return $user['count(id)'] >= 1;
	}

	/**
	 * @param stdClass $params
	 * @return array
	 */
	public function chechPasswordHistory(stdClass $params)
	{
		$aes           = $this->getAES();
		$this->user_id = $params->id;
		$aesPwd        = $aes->encrypt($params->password);
		$this->db->setSQL("SELECT password, pwd_history1, pwd_history2  FROM users WHERE id='" . $this->user_id . "'");
		$pwds = $this->db->fetchRecord();
		if($pwds['password'] == $aesPwd || $pwds['pwd_history1'] == $aesPwd || $pwds['pwd_history2'] == $aesPwd){
			return array('error' => true);
		} else {
			return array('error' => false);
		}
	}

	/**
	 * @param $newpassword
	 * @return mixed
	 */
	public function changePassword($newpassword)
	{
		$aes    = $this->getAES();
		$aesPwd = $aes->encrypt($newpassword);
		$this->db->setSQL("SELECT password, pwd_history1 FROM users WHERE id='$this->user_id'");
		$pwds                = $this->db->fetchRecord();
		$row['password']     = $aesPwd;
		$row['pwd_history1'] = $pwds['password'];
		$row['pwd_history2'] = $pwds['pwd_history1'];
		$sql                 = $this->db->sqlBind($row, 'users', 'U', array('id' => $this->user_id));
		$this->db->setSQL($sql);
		$this->db->execLog();
		return;

	}

	public function changeMyPassword(stdClass $params)
	{
		$this->user_id = $params->id;
		return array('success' => true);
	}

	public function updateMyAccount(stdClass $params)
	{
		$data = get_object_vars($params);
		unset($data['id']);
		$sql = $this->db->sqlBind($data, 'users', 'U', array('id' => $params->id));
		$this->db->setSQL($sql);
		$this->db->execLog();
		return array('success' => true);
	}

	public function verifyUserPass($pass)
	{
		$aes  = new AES($_SESSION['site']['AESkey']);
		$pass = $aes->encrypt($pass);
		$uid  = $_SESSION['user']['id'];
		$this->db->setSQL("SELECT username FROM users WHERE id = '$uid' AND password = '$pass' AND authorized = '1' LIMIT 1");
		$count = $this->db->rowCount();
		return ($count != 0) ? 1 : 2;
	}

	public function getProviders()
	{
		$this->db->setSQL("SELECT u.id, u.fname, u.lname, u.mname
                FROM acl_user_roles AS acl
                LEFT JOIN users AS u ON u.id = acl.user_id
                WHERE acl.role_id = '2'");
		$records   = array();
		$records[] = array(
			'name' => 'All', 'id' => 'all'
		);
		foreach($this->db->fetchRecords(PDO::FETCH_ASSOC) As $row){
			$row['name'] = $this->getUserNameById($row['id']);
			$records[]   = $row;
		}
		return $records;
	}

	public function getUserRolesByCurrentUserOrUserId($uid = null)
	{
		$uid = ($uid == null) ? $_SESSION['user']['id'] : $uid;
		$this->db->setSQL("SELECT * FROM acl_user_roles WHERE user_id = '$uid'");
		return $this->db->fetchRecords(PDO::FETCH_ASSOC);
	}
	
	
	
	//Login & Register methods
	
    /* ------------- `users` table method ------------------ */

    /**
     * Creating new user
     * @param String $name User full name
     * @param String $email User login email id
     * @param String $password User login password
     */
    public function createUser($fname, $email, $password) {
        require_once 'PassHash.php';
        $response = array();

        // First check if user already existed in db
        if (!$this->isUserExists($email)) {
            // Generating password hash
            //$password_hash = PassHash::hash($password);
			
			$aes    = $this->getAES();
			$password_hash = $aes->encrypt($newpassword);

            // Generating API key
            $api_key = $this->generateApiKey();

            // insert query
            /*$stmt = $this->conn->prepare("INSERT INTO users(name, email, password_hash, api_key, status) values(?, ?, ?, ?, 1)");
            $stmt->bind_param("ssss", $name, $email, $password_hash, $api_key);

            $result = $stmt->execute();

            $stmt->close();
			*/
			
			//remove null
			/*foreach($data as $key => $val){
				if($val == null || $val == ''){
					unset($data[$key]);
				}
			}*/
			
			$data=array('fname'=>$fname,'password'=>$password_hash,'email'=>$email,,'authorized'=>0,'active'=>1);
			
			$sql = $this->db->sqlBind($data, 'users', 'I');
			$this->db->setSQL($sql);
			$this->db->execLog();
			$params->id = $this->user_id = $this->db->lastInsertId;
			$params->fullname = Person::fullname($params->fname, $params->mname, $params->lname);
			if($params->password != ''){
				$this->changePassword($params->password);
			}
			$params->password = '';
			$role['user_id']  = $params->id;
			$sql              = $this->db->sqlBind($role, 'acl_user_roles', 'I');
			$this->db->setSQL($sql);
			$this->db->execLog();
			
            // Check for successful insertion
            if ($result) {
                // User successfully inserted
                return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return USER_CREATE_FAILED;
            }
        } else {
            // User with same email already existed in the db
            return USER_ALREADY_EXISTED;
        }

        return $response;
    }

    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($email, $password) {
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT password_hash FROM users WHERE email = ?");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $stmt->bind_result($password_hash);

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Found user with the email
            // Now verify the password

            $stmt->fetch();

            $stmt->close();

            if (PassHash::check_password($password_hash, $password)) {
                // User password is correct
                return TRUE;
            } else {
                // user password is incorrect
                return FALSE;
            }
        } else {
            $stmt->close();

            // user not existed with the email
            return FALSE;
        }
    }

    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isUserExists($email) {
        $stmt = $this->conn->prepare("SELECT id from users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Fetching user by email
     * @param String $email User email id
     */
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT name, email, api_key, status, created_at FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            // $user = $stmt->get_result()->fetch_assoc();
            $stmt->bind_result($name, $email, $api_key, $status, $created_at);
            $stmt->fetch();
            $user = array();
            $user["name"] = $name;
            $user["email"] = $email;
            $user["api_key"] = $api_key;
            $user["status"] = $status;
            $user["created_at"] = $created_at;
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user api key
     * @param String $user_id user id primary key in user table
     */
    public function getApiKeyById($user_id) {
        $stmt = $this->conn->prepare("SELECT api_key FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            // $api_key = $stmt->get_result()->fetch_assoc();
            // TODO
            $stmt->bind_result($api_key);
            $stmt->close();
            return $api_key;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user id by api key
     * @param String $api_key user api key
     */
   /* public function getUserId($api_key) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $stmt->bind_result($user_id);
            $stmt->fetch();
            // TODO
            // $user_id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user_id;
        } else {
            return NULL;
        }
    }*/

    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("SELECT id from users WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }
	
	
	
	
	
	

}

//$u = new User();
//print_r($u->getUserByUsername('demo'));
