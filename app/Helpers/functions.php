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
 * @param  string  $value
 * @param  int     $limit
 * @param  string  $end
 */

if(!function_exists('start_limit')) {
	function start_limit(&$value, $start = 0, $limit = 100, $end = '...')
	{
		$value = rtrim(mb_strimwidth($value, $start, $limit, '', 'UTF-8')) . $end;
	}
}