<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 2017/4/10
 * Time: 下午10:38
 */

/**
 * Limit the number of characters in a string.
 *
 * @param  string $value
 * @param  int    $limit
 * @param  string $end
 */

if(!function_exists('start_limit')) {
	function start_limit(&$value, $start = 0, $limit = 100, $end = '...')
	{
		$value = rtrim(mb_strimwidth($value, $start, $limit, '', 'UTF-8')) . $end;
	}
}

if(!function_exists('file_ext')) {
	function file_ext($file)
	{
		if(!$file) return false;
		$ext = pathinfo($file, PATHINFO_EXTENSION);

		//判断淘宝的特殊图片类型
		return in_array(strtolower($ext), ['ss2']) ? 'jpg' : $ext;
	}
}

if(!function_exists('sql_dump')) {
	function sql_dump()
	{
		DB::listen(
			function ($sql) {
				// $sql is an object with the properties:
				//  sql: The query
				//  bindings: the sql query variables
				//  time: The execution time for the query
				//  connectionName: The name of the connection

				// To save the executed queries to file:
				// Process the sql and the bindings:
				foreach ($sql->bindings as $i => $binding) {
					if ($binding instanceof \DateTime) {
						$sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
					} else {
						if (is_string($binding)) {
							$sql->bindings[$i] = "'$binding'";
						}
					}
				}

				// Insert bindings into query
				$query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);

				$query = vsprintf($query, $sql->bindings);

				// Save the query to file
				$logFile = fopen(
					storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log'),
					'a+'
				);
				fwrite($logFile, date('Y-m-d H:i:s') . ': ' . $query . PHP_EOL);
				fclose($logFile);
			}
		);
	}
}

if(!function_exists('wechat_at')) {
	function wechat_at($msg, $name)
	{
		//用户名、消息为空直接返回当前消息
		if(!$msg || !$name) return $msg;
		$arr = explode($name, $msg);

		return isset($arr[1]) ? trim($arr[1]) : $msg;
	}
}