<?php
class test{

	function start($ts)
	{
		
		
		$serverInfo = $ts->getElement('data', $ts->serverInfo());
		$online = $serverInfo['virtualserver_clientsonline'] - $serverInfo['virtualserver_queryclientsonline'];
		$online_data = array();
		$online_data['channel_name'] = str_replace('%ONLINE%', $online, "* Online: %ONLINE%");
		$ts->editChannel(5, $online_data);

	}
}
?>
