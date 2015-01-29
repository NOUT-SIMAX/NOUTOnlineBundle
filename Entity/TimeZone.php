<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 29/01/2015
 * Time: 10:29
 */

namespace NOUT\Bundle\NOUTSessionManagerBundle\Entity;


class TimeZone 
{
	static public function s_aGetTabTimezone()
	{
		$script_tz = date_default_timezone_get();

		$zones_array = array();
		$timestamp = time();
		foreach(timezone_identifiers_list() as $key => $zone)
		{
			date_default_timezone_set($zone);
			$zones_array[$key]['zone'] = $zone;
			$zones_array[$key]['diff_from_GMT'] = date('P', $timestamp); //'UTC/GMT ' .
		}

		date_default_timezone_set($script_tz);

		usort($zones_array, function($a, $b)
		{
			$diff_a = (int)$a['diff_from_GMT'];
			$diff_b = (int)$b['diff_from_GMT'];

			if ($diff_a < $diff_b)
				return -1;
			else if ($diff_a > $diff_b)
				return 1;

			$zone_a = $a['zone'];
			$zone_b = $b['zone'];

			return strcmp($zone_a, $zone_b);
		});

		return $zones_array;
	}
}