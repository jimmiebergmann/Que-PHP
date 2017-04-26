<?php

class Que
{

	private $socket = NULL;

	public function __construct()
	{
		
	}

	public function __destruct()
	{
		
	}

	public function Connect($address, $port, $timeout)
	{
		if(is_numeric($timeout) == false)
		{
			$timeout = ini_get("default_socket_timeout");
		}

		$this->socket = @fsockopen($address, $port, $errno, $errstr, $timeout); 

		if(!$this->socket)
		{
			return false;
		}

		return true;
	}

	public function Disconnect()
	{

	}

	public function IsConnected()
	{
		
	}

	public function Push($message, $timeout)
	{
		if($this->_Send("PUSH " . strlen($message) . "\n" . $message . "\n") != 0)
		{
			return false;
		}

		$recv = $this->_Receive($timeout);
		if(is_string($recv) == false)
		{
			$this->_Send("ABORT\n");

			return false;
		}

		return $recv;
	}

	public function Pull($timeout)
	{
		
	}

	public function Ack($message)
	{
		
	}

	public function Abort()
	{
		
	}

	private function _Send($data)
	{
		if(fwrite($this->socket, $data) === false)
		{
			return 1;
		}

		return 0;
	}

	private function _Receive($timeout)
	{
		if(is_integer($timeout))
		{
			stream_set_blocking($this->socket, TRUE);
        	stream_set_timeout($this->socket, $timeout);
		}

		$recv = fread($this->socket, 2000);
        $info = stream_get_meta_data($this->socket);

        if ($info['timed_out'])
        {
        	echo "Receive timed out!\n";
        	return false;
	    }
	    
	    return $recv;
	}

}


?>