<?php

class Que
{

	private $socket = NULL;
	private $connected = false;
/*
	public static const $Disconnected = 0;
	public static const $BadFormat = 1;
	public static const $Timeout = 2;
*/

	public function __construct()
	{
		
	}

	public function __destruct()
	{
		
	}

	public function Connect($host, $port, $timeout)
	{
		if(!is_integer($timeout) || $timeout <= 0)
		{
			$timeout = ini_get("default_socket_timeout");
		}

		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($this->socket === false)
		{
			throw new Exception("Que: socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n" );
			return false;
		}

    	if(socket_set_nonblock($this->socket) == false)
    	{
    		throw new Exception("Que: Unable to set nonblock on socket\n" );
			return false;
    	}

		$time = time();
		if(!socket_connect($this->socket, $host, $port))
		{
			$err = socket_last_error();
			if($err != 10035)
			{
				throw new Exception("Que: socket_connect() failed: reason: " . socket_strerror($err) . "\n" );
				return false;
			}
		}

		$write_socket = array($this->socket);
		$null_val = NULL;

		$select_status = socket_select($null_val, $write_socket, $null_val, $timeout);
		if($select_status === false)
		{
			throw new Exception("Que: socket_select() failed: reason: "  . socket_strerror(socket_last_error()) . "\n" );
			return false;
		}
		else if($select_status == 0)
		{
			return false;
		}

		/*if(socket_set_block($this->socket) == false)
    	{
    		throw new Exception("Que: Unable to set block on socket\n" );
			return false;
    	}
    	*/

    	$this->connected = true;
		


		// Create the socket
		/*$this->socket = @fsockopen($address, $port, $errno, $errstr, $timeout); 

		if(!$this->socket)
		{
			return false;
		}

		// Make sure that the socket is blocking.
		stream_set_blocking($this->socket, TRUE);*/

		return true;
	}

	public function Disconnect()
	{
		if($this->socket !== NULL)
		{
			socket_close($this->socket);
			$this->socket = NULL;
			$this->connected = false;
		}
	}

	public function IsConnected()
	{
		return $connected;
	}

	public function Push($message, $timeout, $abort_timeout = 1)
	{
		// Send push message.
		if($this->_Send("PUSH " . strlen($message) . "\n" . $message . "\n") == 0)
		{
			return false;
		}

		// Receive ack.
		$recv = $this->_Receive($timeout);
		if(is_string($recv) == false)
		{
			if(Abort($abort_timeout) == false)
			{
				$this->Disconnect();
				return -1;
			}
			return 0;
		}

		// parse answer.
		$newline_pos = strpos($recv, '\n');
		//if($newline_pos == 0)

		/*$recv = $this->_Receive($timeout);
		if(is_string($recv) == false)
		{
			$this->_Send("ABORT\n");
			return false;
		}*/

		return "No message";
	}

	public function Pull($timeout)
	{
		
	}

	public function Ack($message)
	{
		
	}

	public function Abort($timeout = NULL)
	{
		
	}

	private function _Send($data)
	{
		if($this->connected == false)
		{
			return 0;
		}

		$data_len = strlen($data);
		if(($send_size = @socket_write($this->socket, $data, $data_len)) === false)
		{
			$this->Disconnect();
			return 0;
		}

		if($send_size != $data_len)
		{
			$this->Disconnect();
			return 0;
		}

		return $send_size;
	}

	private function _Receive(&$data, $timeout)
	{
		$read_socket = array($this->socket);
		$null_val = NULL;

		$select_status = socket_select($read_socket, $null_val, $null_val, $timeout);
		if($select_status === false)
		{
			$this->Disconnect();
			return -1;
		}
		else if($select_status == 0)
		{
			return 0;
		}

		// Read data
		if(($recv = socket_read($this->socket, 65535)) === false)
		{
			$this->Disconnect();
			return -1;
		}

		return $recv;


		/*
		$recv = fread($this->socket, 2000);
        $info = stream_get_meta_data($this->socket);

        if ($info['timed_out'])
        {
        	echo "timeout\n";
        	return false;
	    }
	    
		echo "RECV: " . $recv . "\n";

	    return $recv;
	    */
	}

}


?>