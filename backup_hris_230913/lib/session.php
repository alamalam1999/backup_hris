<?php

class session
{
	var $session_name	= 'ox_session';
	var $session_expire	= 3600;
	var $session_path	= '';
	var $session_domain	= '';
	var $userdata;
	
	function __construct($DB = '',$params = array())
	{
		$this->DB = $DB;
		$this->now = time();
		
		if ( ! $this->_read())
		{
			$this->_create();
		}
		else
		{
			$this->_update();
		}
	}

	function _create()
	{
		$sessid = mt_rand(0, mt_getrandmax());
		$this->userdata = array(
							'session_id'	=> md5(uniqid($sessid, TRUE)),
							'ip_address'	=> $this->get_ip(),
							'last_activity'	=> $this->now,
							'user_data'		=> ''
							);

		$this->_set_cookie();
	}
	
	function _read()
	{
		if( ! isset($_COOKIE[$this->session_name]) )
		{
			return FALSE;
		}
		
		$session = $_COOKIE[$this->session_name];
		$this->userdata = $this->_unserialize($session);
		
		if ( ! is_array($this->userdata) OR ! isset($this->userdata['session_id']) OR ! isset($this->userdata['ip_address']) OR ! isset($this->userdata['last_activity']))
		{
			$this->_destroy();
			return FALSE;
		}
		
		if (($this->userdata['last_activity'] + $this->session_expire) < $this->now)
		{
			$this->_destroy();
			return FALSE;
		}
		
		return TRUE;
	}

	function _write()
	{
		$this->_set_cookie();
	}

	function _update()
	{
		$this->userdata['last_activity'] = $this->now;
		$this->_set_cookie();
	}
	
	function _destroy()
	{
		setcookie(
			$this->session_name,
			addslashes(serialize(array())),
			(time() - 31500000),
			$this->session_path,
			$this->session_domain,
			0
		);
	}

	function userdata($item)
	{
		return ( ! isset($this->userdata[$item])) ? FALSE : $this->userdata[$item];
	}
	
	function set_userdata($newdata = array(), $newval = '')
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$this->userdata[$key] = $val;
			}
		}

		$this->_write();
	}
	
	function unset_userdata($newdata = array())
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => '');
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				unset($this->userdata[$key]);
			}
		}

		$this->_write();
	}
	
	function _set_cookie($cookie_data = NULL)
	{
		if (is_null($cookie_data))
		{
			$cookie_data = $this->userdata;
		}

		$cookie_data = $this->_serialize($cookie_data);
		$expire = $this->session_expire + time();

		setcookie(
					$this->session_name,
					$cookie_data,
					$expire,
					$this->session_path,
					$this->session_domain,
					0
				);
	}
	
	function _serialize($data)
	{
		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				if (is_string($val))
				{
					$data[$key] = str_replace('\\', '{{slash}}', $val);
				}
			}
		}
		else
		{
			if (is_string($data))
			{
				$data = str_replace('\\', '{{slash}}', $data);
			}
		}

		return serialize($data);
	}

	function _unserialize($data)
	{ 
		$data = @unserialize(stripslashes($data));

		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				if (is_string($val))
				{
					$data[$key] = str_replace('{{slash}}', '\\', $val);
				}
			}

			return $data;
		}

		return (is_string($data)) ? str_replace('{{slash}}', '\\', $data) : $data;
	}

	function get_ip()
	{
		$IP = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		return $IP; #isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $IP;
	}
}