<?php

namespace Core\Model;

abstract class BaseFacade
{
	protected $db;

	public function __construct(\Nette\Database\Context $context)
	{
		$this->db = $context;
	}
}
