<?php

$_ACTION_EVENTS = array();
$_FILTER_EVENTS = array();
 
function hook_action($event)
{
	global $_ACTION_EVENTS;

	if(isset($_ACTION_EVENTS[$event]))
	{
		foreach($_ACTION_EVENTS[$event] as $func)
		{
			if( function_exists($func)){
				call_user_func($func);
			}
		}
	}
}

function register_action($event, $func)
{
	global $_ACTION_EVENTS;
	$_ACTION_EVENTS[$event][] = $func;
}

function hook_filter($event,$content)
{
    global $_FILTER_EVENTS;

	if(isset($_FILTER_EVENTS[$event]))
	{
		foreach($_FILTER_EVENTS[$event] as $func) {
			if( method_exists($func[0],$func[1])) {
				$params = array_merge(array($content), $func[2]);
				$content = call_user_func_array(array($func[0],$func[1]), $params);
			}
		}
	}
	return $content;
}

function register_filter($event, $class, $obj, $params = array())
{
	global $_FILTER_EVENTS;
	$_FILTER_EVENTS[$event][] = array($class,$obj,$params);
}