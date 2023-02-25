<?php

namespace App\Service;

use Illuminate\Database\Eloquent\Collection;

class Paginator
{
	private $pages_count;
	private $step;
	private $data;

	function __construct(Collection $data, int $step)
	{
		$this->data = $data;
		$this->step = $step;
		$this->pages_count = ceil($data->count() / $step);
	}

	function getData(int $page_number)
	{
		$start = $page_number == 1 ? 0 : $page_number * $this->step - $this->step;

		return $this->data->skip($start)->take($this->step)->toArray();
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