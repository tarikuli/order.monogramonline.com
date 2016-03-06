<?php

namespace App\Http\Controllers;

use App\Occasion;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\OccasionCreateRequest;
use App\Http\Requests\OccasionUpdateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OccasionController extends Controller
{
	public function index ()
	{
		$occasions = Occasion::where('is_deleted', 0)
							 ->orderBy(DB::raw('occasion_display_order + 0'), 'asc')
							 ->latest()
							 ->paginate(50);
		$count = 1;

		return view('occasions.index', compact('occasions', 'count'));
	}

	public function create ()
	{
		return view('occasions.create');
	}

	public function store (OccasionCreateRequest $request)
	{
		$occasion = new Occasion();
		$occasion->occasion_code = $request->get('occasion_code');
		$occasion->occasion_description = $request->get('occasion_description');
		$occasion->occasion_display_order = intval($request->get('occasion_display_order'));

		$occasion->save();

		session()->flash('success', 'Occasion is added successfully.');

		return redirect(route('occasions.index'));
	}

	public function show ($id)
	{
		return redirect(route('occasions.index'));
		$production_categories = Occasion::where('is_deleted', 0)
										   ->find($id);
		if ( !$production_categories ) {
			return view('errors.404');
		}

		return view('production_categories.show', compact('category'));
	}

	public function edit ($id)
	{
		return redirect(route('occasions.index'));

		$production_categories = Occasion::where('is_deleted', 0)
												   ->find($id);
		if ( !$production_categories ) {
			return view('errors.404');
		}

		return view('production_categories.edit', compact('category'));
	}

	public function update (OccasionUpdateRequest $request, $id)
	{
		$occasion = Occasion::where('is_deleted', 0)
								->find($id);
		if ( !$occasion ) {
			return view('errors.404');
		}

		$occasion->occasion_code = $request->get('occasion_code');
		$occasion->occasion_description = $request->get('occasion_description');
		$occasion->occasion_display_order = intval($request->get('occasion_display_order'));

		$occasion->save();

		session()->flash('success', 'Occasion is updated successfully.');

		return redirect(route('occasions.index'));
	}

	public function destroy ($id)
	{
		$occasion = Occasion::where('is_deleted', 0)
								->find($id);
		if ( !$occasion ) {
			return view('errors.404');
		}

		$occasion->is_deleted = 1;
		$occasion->save();

		session()->flash('success', sprintf('Occasion %s is deleted successfully.', $occasion->occasion_code));

		return redirect(route('occasions.index'));
	}
}
