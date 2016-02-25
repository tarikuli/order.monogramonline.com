<?php

namespace App\Http\Controllers;

use App\MasterCategory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class MasterCategoryController extends Controller
{
	private function getChildCategories ($id = 0, $is_ajax = false)
	{
		$id = intval($id);

		$categories = null;
		$categories = MasterCategory::where('parent', $id)
									->where('is_deleted', 0)
									->orderBy(DB::raw('master_category_display_order + 0'), 'asc')
									->lists('master_category_description', 'id');
		if ( $is_ajax ) {
			$categories->prepend('Select a category', '');
			return view('master_categories.ajax_category_response', compact('categories', 'id'));
		}
		$categories->prepend('Select a category', '0');

		return $categories;
	}

	public function index ()
	{
		$master_categories = MasterCategory::where('parent', 0)
										   ->where('is_deleted', 0)
										   ->orderBy(DB::raw('master_category_display_order + 0'), 'asc')
										   ->paginate(50);
		$count = 1;

		$categories = $this->getChildCategories();

		return view('master_categories.new_index', compact('master_categories', 'count', 'categories'));
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
		$master_category->parent = intval($request->get('parent_category'));

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

	public function getNext (Request $request, $parent_category_id)
	{
		if ( !$request->ajax() ) {
			return view('errors.404');
		}
		if(intval($parent_category_id) == 0){
			return;
		}
		return $this->getChildCategories($parent_category_id, true);
	}
}
