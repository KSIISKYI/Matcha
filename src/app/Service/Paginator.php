<?php

namespace App\Service;

class Paginator
{
	private $pages_count;
	private $step;
	private $curret_page_number;
	private $data;

	function __construct(array $data, int $step)
	{
		$this->data = $data;
		$this->step = $step;
		$this->pages_count = ceil(count($data) / $step);
	}

	function getData(int $page_number)
	{
		if ($page_number > $this->pages_count) {
			return [];
		}

		$start = $page_number == 1 ? 0 : $page_number * $this->step - $this->step;

		return array_slice($this->data, $start, $this->step);
	}

	function getPageObj($page_number)
	{
		return [
			'current_page_number' => $page_number,
			'has_previous' => $page_number > 1,
			'previous_page_number' => $page_number - 1,
			'has_next' => $page_number < $this->pages_count,
			'next_page_number' => $page_number + 1,
            'has_other_pages' => $this->pages_count > 1,
            'last_page_number' => $this->pages_count,
		];
	}
}