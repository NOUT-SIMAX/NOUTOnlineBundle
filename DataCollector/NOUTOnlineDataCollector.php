<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 25/07/14
 * Time: 09:27
 */

namespace NOUT\Bundle\NOUTOnlineBundle\DataCollector;



use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class NOUTOnlineDataCollector  extends DataCollector {

	private $m_clLogger;

	public function getName()
	{
		return 'NOUTOnline';
	}

	public function __construct(NOUTOnlineLogger $clLogger)
	{
		$this->m_clLogger=$clLogger;
	}

	public function collect(Request $request, Response $response, \Exception $exception = null)
	{
		$queries = array();
		$queries = $this->m_clLogger->m_TabQueries;

		$this->data = array(
			'queries'     => $queries,
		);
	}

	public function getQueryCount()
	{
		return count($this->data['queries']);
	}

	public function getQueries()
	{
		return $this->data['queries'];
	}

	public function getTime()
	{
		$time = 0;
		foreach ($this->data['queries'] as $query) {
			$time += $query['executionMS'];
		}

		return $time;
	}
}