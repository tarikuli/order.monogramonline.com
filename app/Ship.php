<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
	protected $table = 'shipping';

	private function tableColumns ()
	{
		$columns = $this->getConnection()
						->getSchemaBuilder()
						->getColumnListing($this->getTable());

		return array_slice($columns, 1, -2);
	}

	public static function getTableColumns ()
	{
		return (new static())->tableColumns();
	}
}
