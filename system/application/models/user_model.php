<?php

/**
 * User_model
 * @project PumpkinEngine
 * @package Users
 */

class User_Model extends Model
{
	/** Вспомогательные **/
	
	function _required($required, $data)
	{
		foreach($required as $field)
			if(!isset($data[$field])) return false;
		
		return true;
	}
	
	function _default($defaults, $options)
	{
		return array_merge($defaults,$options);
	}
	
	/** Пользовательские методы**/
	
	/**
	 * AddUser метод создания записи в таблице users
	 *
	 * Option: Values
	 * --------------
	 * userName
	 * userEmail
	 * userPassword
	 * userType
	 * userStatus
	 * 
	 * @param array $options
	 * @result int insert_id()
	 */
	function AddUser($options = array())
	{
		// required values
		if(!$this->_required(
			array('userName','userPassword'),
			$options)
		) return false;
		
		// default values
		$options = $this->_default(array('userStatus' => 'active'), $options);
		$this->db->insert('users', $options);
		return $this->db->insert_id();
	}
	
	/**
	 * UpdateUser метод редактирования записи в таблице users
	 *
	 * Option: Values
	 * --------------
	 * userId			required
	 * userName
	 * userPassword
	 * userType
	 * userStatus
	 *
	 * @param array $options
	 * @return int affected_rows()
	 */
	function UpdateUser($options = array())
	{
		// required values
		if(!$this->_required(
			array('userId'),
			$options)
		) return false;
		
		// set values
		if(isset($options['userName']))
			$this->db->set('userName', $options['userName']);
		if(isset($options['userPassword']))
			$this->db->set('userPassword', md5($options['userPassword']));
		if(isset($options['userType']))
			$this->db->set('userType', $options['userType']);
		if(isset($options['userStatus']))
			$this->db->set('userStatus', $options['userStatus']);
		
		$this->db->where('userId', $options['userId']);
		$this->db->update('users');
		
		return $this->db->affected_rows();
	}
	
	/**
	 * GetUsers метод возвращает компетентный список пользователей из таблицы users
	 *
	 * Option: Values
	 * --------------
	 * userId
	 * userName
	 * userPassword
	 * userType
	 * userStatus
	 *
	 * limit			limit the returned records
	 * offset			bypass this many records
	 * sortBy			sort by this column
	 * sortDirection	(asc, desc)
	 *
	 * Returned Object (array of)
	 * --------------------------
	 * userId
	 * userName
	 * userPassword
	 * userType
	 * userStatus
	 *
	 * @param array $options
	 * @return array of objects
	 */
	function GetUsers($options = array())
	{
		// where values
		if(isset($options['userId']))
			$this->db->where('userId', $options['userId']);
		if(isset($options['userName']))
			$this->db->where('userName', $options['userName']);
		if(isset($options['userPassword']))
			$this->db->where('userPassword', $options['userPassword']);
		if(isset($options['userType']))
			$this->db->where('userType', $options['userType']);
		if(isset($options['userStatus']))
			$this->db->where('userStatus', $options['userStatus']);
		
		// limit / offset
		if(isset($options['limit']) && isset($options['offset']))
			$this->db->limit($options['limit'], $options['offset']);
		else if(isset($options['limit']))
			$this->db->limit($options['limit']);
		
		// sort values
		if(isset($options['sortBy']) && isset($options['sortDirection']))
			$this->db->order_by($options['sortBy'], $options['sortDirection']);
		
		if(!isset($options['userStatus'])) $this->db->where('userStatus !=' , 'deleted');
		
		$query = $this->db->get("users");
		
		if(isset($options['count'])) return $query->num_rows();
		if(isset($options['userId']) || isset($options['userName']))
			return $query->row(0);
			
		return $query->result();
	}
	
	/* Методы идентификации и авторизации */
	
	/**
	 * Login метод добавляет информацию о пользователе из базы данных в сессию
	 * 
	 * Option: Values
	 * ---------------
	 * userName
	 * userPassword
	 *
	 * @param array $options
	 * @return object result()
	 */
	function Login($options = array())
	{
		// required values
		if(!$this->_required(
			array('userName','userPassword'),
			$options)
		) return false;
		
		$user = $this->GetUsers(array('userName' => $options['userName'], 'userPassword' => md5($options['userPassword'])));
		if(!$user) return false;
		
		$this->session->set_userdata('userName', $user->userName);
		$this->session->set_userdata('userId', $user->userId);
		$this->session->set_userdata('userType', $user->userType);
		$this->session->set_userdata('userRank', $user->userRank);
		return true;
	}
	
	/**
	 * Secure метод проверки пользовательской сессии на запрашиваемые данные из нее
	 * acces to a specific area
	 *
	 * Option: Values
	 * --------------
	 * userType
	 * 
	 * @param array $options
	 * @return bool
	 */
	function Secure($options = array())
	{
		// required values
		if(!$this->_required(
			array('userType'),
			$options)
		) return false;
		
		$userType = $this->session->userdata('userType');
		
		if(is_array($options['userType']))
		{
			foreach($options['userType'] as $optionUserType)
			{
				if($optionUserType == $userType) return true;
			}
		}
		else
		{
			if($userType == $options['userType']) return true;
		}
		
		return false;
	}
}