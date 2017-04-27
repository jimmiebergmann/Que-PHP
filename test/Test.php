<?php

require_once("../src/Que.php");


ProducerTest();

/*
echo 'Type "consumer" or "producer" to start: ';

$test_type_error = false;
do
{
	$test_type = strtoupper(GetInput());

	if($test_type == "CONSUMER")
	{
		ConsumerTest();
	}
	else if($test_type == "PRODUCER")
	{
		ProducerTest();
	}
	else
	{
		$test_type_error = true;
		echo "Invalid input, try again: ";
	}
} while($test_type_error == true);
*/

function GetInput()
{
	if (PHP_OS == 'WINNT')
	{
	  $line = stream_get_line(STDIN, 1024, PHP_EOL);
	}
	else
	{
	  $line = readline('');
	}

	return $line;
}

function ConsumerTest()
{
	
}

function ProducerTest()
{
	$que = new Que();
	if($que->Connect("127.0.0.1", 11400, 3) == false)
	{
		echo "Failed to connect to server.\n";
		die;
	}
	
	echo "Connected!\n";


	while(1)
	{
		$message = "Hello world!";
		$timeout = 3;
		$response = $que->Push($message, $timeout);

		if(is_string($response) == false)
		{
			echo "-----------------------------\n";
			break;
		}

		echo "WWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWW\n";


	}

	echo "Disconnected.\n";
}



?>