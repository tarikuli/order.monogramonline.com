<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
	public function triggers ()
	{
		return $this->hasMany('App\RuleTrigger', 'rule_id', 'id');
    }
	public function actions ()
	{
		return $this->hasMany('App\RuleAction', 'rule_id', 'id');
    }
}
