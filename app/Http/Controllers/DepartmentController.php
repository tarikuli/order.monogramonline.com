<?php

namespace App\Http\Controllers;

use App\Station;
use App\Department;
use App\DepartmentStation;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\DepartmentCreateRequest;
use App\Http\Requests\DepartmentUpdateRequest;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{

	public function index ()
	{
		$count = 1;
		$stations = Station::where('is_deleted', 0)
						   ->lists('station_description', 'id');
		$departments = Department::with('stations_list')
								 ->where('is_deleted', 0)
								 ->latest()
								 ->paginate(50);

		return view('departments.index', compact('departments', 'count', 'stations'));
	}
	
	public function create ()
	{
		$stations = Station::where('is_deleted', 0)
						   ->lists('station_description', 'id');

		return view('departments.create', compact('stations'));
	}

	public function store (DepartmentCreateRequest $request)
	{
		#return $request->all();
		$department = new Department();
		$department->department_code = $request->get('department_code');
		$department->department_name = $request->get('department_name');
		$department->save();
		$department->stations()
				   ->attach($request->get('department_stations'));

		return redirect(url('departments'));
	}

	public function update (DepartmentUpdateRequest $request, $id)
	{
		$department = Department::find($id);
		$department->department_code = $request->get('department_code');
		$department->department_name = $request->get('department_name');
		$department->save();

		$updateStationText = preg_replace('/\s+/', '', $request->get('department_stations'));
		$updatedStationsArray = explode(",", $updateStationText);
		$newStations = Station::whereIn('station_name', $updatedStationsArray)
							  ->orderByRaw(sprintf("FIELD (station_name, '%s')", implode("', '", $updatedStationsArray)))
							  ->lists('id')
							  ->toArray();
		$department->stations()
				   ->detach();
		$department->stations()
				   ->attach($newStations);

		return redirect(url('departments'));
	}

	public function destroy ($id)
	{
		$department = BatchRoute::find($id);
		$department->is_deleted = 1;
		$department->save();

		return redirect(url('department'));
	}
}



