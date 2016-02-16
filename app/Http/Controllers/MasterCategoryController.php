<?php

namespace App\Http\Controllers;

use App\MasterCategory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MasterCategoryController extends Controller
{
	public function index ()
	{
		$master_categories = MasterCategory::where('is_deleted', 0)
										   ->orderBy(DB::raw('master_category_display_order + 0'), 'asc')
										   ->paginate(50);
		$count = 1;

		return view('master_categories.index', compact('master_categories', 'count'));
	}

	public function create ()
	{
		return view('master_categories.create');
	}

	public function store (Requests\MasterCategoryCreateRequest $request)
	{
		$master_category = new MasterCategory();
		$master_category->master_category_code = $request->get('master_category_code');
		$master_category->master_category_description = $request->get('master_category_description');
		$master_category->master_category_display_order = intval($request->get('master_category_display_order'));

		$master_category->save();

		return redirect(url('master_categories'));
	}

	public function show ($id)
	{
		$master_category = MasterCategory::where('is_deleted', 0)
										 ->find($id);
		if ( !$master_category ) {
			return view('errors.404');
		}

		return view('master_categories.show', compact('master_category'));
	}

	public function edit ($id)
	{
		$master_category = Category::where('is_deleted', 0)
								   ->find($id);
		if ( !$master_category ) {
			return view('errors.404');
		}

		return view('master_categories.edit', compact('master_category'));
	}

	public function update (Requests\MasterCategoryUpdateRequest $request, $id)
	{
		$master_category = MasterCategory::where('is_deleted', 0)
										 ->find($id);
		if ( !$master_category ) {
			return view('errors.404');
		}

		$master_category->master_category_code = $request->get('master_category_code');
		$master_category->master_category_description = $request->get('master_category_description');
		$master_category->master_category_display_order = intval($request->get('master_category_display_order'));

		$master_category->save();

		return redirect(url('master_categories'));
	}

	public function destroy ($id)
	{
		$master_category = MasterCategory::where('is_deleted', 0)
										 ->find($id);
		if ( !$master_category ) {
			return view('errors.404');
		}

		$master_category->is_deleted = 1;
		$master_category->save();

		return redirect(url('master_categories'));
	}
}
