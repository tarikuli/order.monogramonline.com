<?php

namespace App\Http\Controllers;

use App\Department;
use App\RejectionMessage;
use App\RejectionReason;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class RejectionReasonController extends Controller
{
	public function index ()
	{
		$departments = Department::with('stations')
								 ->where('is_deleted', 0)
								 ->get();
		#return $departments;
		$departments_list = $departments->lists('department_name', 'id')
										->prepend('Select a department', '0');

		$stations_list = new Collection();
		foreach ( $departments as $department ) {
			if ( !$department->stations ) {
				continue;
			}
			$stations = $department->stations->lists('station_description', 'id')
											 ->prepend('Select a station', '0');
			$stations_list->prepend($stations, $department->id);
		}

		$rejection_reasons = RejectionReason::where('is_deleted', 0)
											  ->paginate(50);

		#return $stations_list;
		$count = 1;

		return view('rejection_reasons.index', compact('departments_list', 'stations_list', 'rejection_reasons', 'count'));
	}

	public function create ()
	{
		//
	}

	public function store (Requests\RejectionReasonCreateRequest $request)
	{
		$department_id = $request->get('department_id') ?: null;
		$station_id = $request->get('station_id') ?: null;
		$rules = [ ];
		if ( $department_id && $department_id != 0 ) {
			$rules['department_id'] = 'exists:departments,id';
		}
		if ( $station_id && $station_id != 0 ) {
			$rules['station_id'] = 'exists:stations,id';
		}

		$validation = Validator::make([
			'department_id' => $department_id,
			'station_id'    => $station_id,
		], $rules);

		if ( $validation->fails() ) {
			return redirect()
				->back()
				->withInput()
				->withErrors($validation);
		}

		$rejection_reason = new RejectionReason();
		$rejection_reason->department_id = $department_id;
		$rejection_reason->station_id = $station_id;
		$rejection_reason->rejection_message = trim($request->get('rejection_message'));
		$rejection_reason->save();

		return redirect(route('rejection_reasons.index'));
	}

	public function show ($id)
	{
		//
	}

	public function edit ($id)
	{
		//
	}

	public function update (Requests\RejectionReasonUpdateRequest $request, $id)
	{
		$rejection_reason = RejectionReason::find($id);
		if ( !$rejection_reason ) {
			return redirect()
				->back()
				->withInput()
				->withErrors([
					'invalid' => 'Cannot update. rejection message id invalid',
				]);
		}

		$department_id = $request->get('updated_department_id') ?: null;
		$station_id = $request->get('updated_station_id') ?: null;
		$rules = [ ];
		if ( $department_id && $department_id != 0 ) {
			$rules['department_id'] = 'exists:departments,id';
		}
		if ( $station_id && $station_id != 0 ) {
			$rules['station_id'] = 'exists:stations,id';
		}

		$validation = Validator::make([
			'department_id' => $department_id,
			'station_id'    => $station_id,
		], $rules);

		if ( $validation->fails() ) {
			return redirect()
				->back()
				->withInput()
				->withErrors($validation);
		}
		$rejection_reason->department_id = $department_id;
		$rejection_reason->station_id = $station_id;
		$rejection_reason->rejection_message = trim($request->get('updated_rejection_message'));
		$rejection_reason->save();

		return redirect(route('rejection_reasons.index'));
	}

	public function destroy ($id)
	{
		$rejection_reason = RejectionReason::find($id);
		if ( !$rejection_reason ) {
			return redirect()
				->back()
				->withInput()
				->withErrors([
					'invalid' => 'Cannot update. rejection message id invalid',
				]);
		}
		$rejection_reason->is_deleted = 1;
		$rejection_reason->save();

		return redirect(route('rejection_reasons.index'));

	}
}
