<?php

/**
 * Update_model
 *
 * @package Updates
 */
 
class Update_model extends Model
{
	/** Utility methods **/
	
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
	
	/** User Methods**/
	
	/**
	 * AddUpdate метод создания записи в таблице updates
	 *
	 * Option: Values
	 * --------------
	 * updateContent			required
	 * userId					required
	 * 
	 * @param array $options
	 * @result int insert_id()
	 */
	function AddUpdate($options = array())
	{
		// required values
		if(!$this->_required(
			array('updateContent','userId'),
			$options)
		) return false;
		
		// default values
		$options = $this->_default(array('updateStatus' => 'active'), $options);
		$options = $this->_default(array('updateDate' => mktime()), $options);
		
		// add values
		$this->db->insert('updates', $options);
		return $this->db->insert_id();
	}

	/**
	 * UpdateUpdate метод редактирования записи в таблице updates
	 *
	 * Option: Values
	 * --------------
	 * updateId				required
	 * updateContent		required
	 * updateDate
	 * updateStatus
	 *
	 * @param array $options
	 * @return int affected_rows()
	 */
	function UpdateUpdate($options = array())
	{
		// required values
		if(!$this->_required(
			array('updateId'),
			$options)
		) return false;
		
		// set values
		if(isset($options['updateContent']))
			$this->db->set('updateContent', $options['updateContent']);
		if(isset($options['updateDate']))
			$this->db->set('updateDate', $options['updateDate']);
		if(isset($options['updateStatus']))
			$this->db->set('updateStatus', $options['updateStatus']);
		
		$this->db->where('updateId', $options['updateId']);
		$this->db->update('updates');
		
		return $this->db->affected_rows();
	}
	
	/**
	 * GetUpdates метод возвращает компетентный список страниц из таблицы updates
	 *
	 * Option: Values
	 * --------------
	 * updateId
	 * updateContent
	 * updateDate
	 * updateStatus
	 * userId
	 *
	 * limit			limit the returned records
	 * offset			bypass this many records
	 * sortBy			sort by this column
	 * sortDirection	(asc, desc)
	 *
	 * Returned Object (array of)
	 * --------------------------
	 * updateId
	 * updateContent
	 * updateDate
	 * updateStatus
	 * userId
	 *
	 * @param array $options
	 * @return array of objects
	 */
	function GetUpdates($options = array())
	{
		// select
		if(isset($options['select']))
			$this->db->select($options['select']);
		
		// join
		if(isset($options['join']))
			$this->db->join($options['join']['table'], $options['join']['on']);
		
		// where values
		if(isset($options['updateId']))
			$this->db->where('updateId', $options['updateId']);
		if(isset($options['updateContent']))
			$this->db->where('updateContent', $options['updateContent']);
		if(isset($options['updateDate']))
			$this->db->where('updateDate', $options['updateDate']);
		if(isset($options['updateStatus']))
			$this->db->where('updateStatus', $options['updateStatus']);
		
		// limit / offset
		if(isset($options['limit']) && isset($options['offset']))
			$this->db->limit($options['limit'], $options['offset']);
		else if(isset($options['limit']))
			$this->db->limit($options['limit']);
		
		// sort values
		if(isset($options['sortBy']) && isset($options['sortDirection']))
			$this->db->order_by($options['sortBy'], $options['sortDirection']);
		
		if(!isset($options['updateStatus'])) $this->db->where('updateStatus !=' , 'deleted');
		
		$query = $this->db->get("updates");
		
		if(isset($options['count']))
			return $query->num_rows();
		
		if(isset($options['updateId']))
			return $query->row(0);
			
		return $query->result();
	}
	
	
	
	/**
	 * ArrayPages метод возвращает нумерацию страниц из таблицы updates
	 *
	 * Option: Values
	 * --------------
	 * base_url				required
	 * total_rows			required
	 * per_page				required
	 * uri_segment			required
	 * next_link			
	 * next_tag_open
	 * next_tag_close
	 * prev_link
	 * prev_tag_open
	 * prev_tag_close
	 * cur_tag_open
	 * cur_tag_close
	 * num_tag_open
	 * num_tag_close
	 * 
	 * 
	 * @param array $options
	 * @return array
	 */
	function ArrayPages($options = array())
	{
		// required values
		$options['uri_segment'] = str_replace('/', '', $options['uri_segment']);
		$options['uri_segment'] = ($options['uri_segment'] != '') ? $options['uri_segment'] : 1;
		if(!$this->_required(
			array('base_url', 'total_rows', 'per_page', 'uri_segment'),
			$options)
		) return false;
		$return = array();
		
		
		
		if($options['uri_segment'] == 1){
			$pages = $options['prev_tag_open'].$options['prev_link'].$options['prev_tag_close'];
			array_push($return, $pages);
		}else{
			$get_r = $options['uri_segment'] - 1;
			$pages = '<a href="'.$options['base_url'].$get_r.'/">'.$options['prev_tag_open'].$options['prev_link'].$options['prev_tag_close'].'</a>';
			array_push($return, $pages);
		}
		$w = ceil($options['total_rows']/$options['per_page']);
		$z = 1;
		while($z <= $w){
			if($z != $options['uri_segment']){
				$pages = '<a href="'.$options['base_url'].$z.'/">'.$options['num_tag_open'].$z.$options['num_tag_close'].'</a>';
				array_push($return, $pages);
			}else{
				$pages = $options['cur_tag_open'].$z.$options['cur_tag_close'];
				array_push($return, $pages);
			}
			$z++;
		}
		$z--;
		if($z != $options['uri_segment']){
			$get_w = $options['uri_segment'] + 1;
			$pages = '<a href="'.$options['base_url'].$get_w.'/">'.$options['next_tag_open'].$options['next_link'].$options['next_tag_close'].'</a>';
			array_push($return, $pages);
		}else{
			$pages = $options['next_tag_open'].$options['next_link'].$options['next_tag_close'];
			array_push($return, $pages);
		}
		return $return;
	}
	
	
	
	
	function declOfNum($number, $titles)
	{
		$cases = array (2, 0, 1, 1, 1, 2);
		return $number." ".$titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
	}
	function Interval($date)
	{
		$interval = mktime()-$date;
		if($interval == 0)
			return 'Только что';
		if($interval < 60)
			return $this->declOfNum($interval, array('секунду', 'секунды', 'секунд')).' назад';
		
		if($interval > 60 && $interval < 60*60)
			return $this->declOfNum(floor($interval/60), array('минуту', 'минуты', 'минут')).' назад';
			
		if($interval > 60*60 && $interval < 60*60*24)
			return $this->declOfNum(floor($interval/(60*60)), array('час', 'часа', 'часов')).' назад';
		
		if($interval > 60*60*24 && $interval < 60*60*24*7)
			return $this->declOfNum(floor($interval/(60*60*24)), array('день', 'дня', 'дней')).' назад';
		
		if($interval > 60*60*24*7 && $interval < 60*60*24*30)
			return $this->declOfNum(floor($interval/(60*60*24*7)), array('неделю', 'недели', 'недель')).' назад';
			
		if($interval > 60*60*24*30 && $interval < 60*60*24*365)
			return $this->declOfNum(floor($interval/(60*60*24)), array('месяц', 'месяца', 'месяцев')).' назад';
		
		if($interval > 60*60*24*365)
			return $this->declOfNum(floor($interval/(60*60*24*365)), array('год', 'года', 'лет')).' назад';
	}
}