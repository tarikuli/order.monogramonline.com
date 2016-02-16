<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;

class UserController extends Controller
{
	public function index ()
	{
		$count = 1;
		$users = User::with('roles')
					 ->where('is_deleted', 0)
					 ->latest()
					 ->paginate(50);

		return view('users.index', compact('users', 'count'));
	}

	public function create ()
	{
		$roles = Role::lists('display_name', 'id');

		return view('users.create', compact('roles'));
	}

	public function store (UserRequest $request)
	{

		$user = new User();
		$user->username = trim($request->get('username'));
		$user->email = $request->get('email');
		$user->password = $request->get('password');
		$user->vendor_id = $request->get('vendor_id');
		$user->zip_code = $request->get('zip_code');
		$user->state = $request->get('state');
		$user->save();

		$role = Role::find($request->get('role'));
		$user->attachRole($role);

		return redirect(url('users'));

	}

	public function show ($id)
	{
		$user = User::where('is_deleted', 0)
					->find($id);
		if ( !$user ) {
			return view('errors.404');
		}

		return view('users.show', compact('user'));
	}

	public function edit ($id)
	{
		$user = User::where('is_deleted', 0)
					->find($id);
		if ( !$user ) {
			return view('errors.404');
		}
		$given_role = $user->roles[0]->id;
		$roles = Role::lists('display_name', 'id');

		return view('users.edit', compact('user', 'roles', 'given_role'));
	}

	public function update (UserUpdateRequest $request, $id)
	{
		$user = User::where('is_deleted', 0)
					->find($id);
		if ( !$user ) {
			return view('errors.404');
		}
		$user->username = trim($request->get('username'));
		if ( $request->has('email') ) {
			$user->email = $request->get('email');
		}
		if ( $request->has('password') ) {
			$user->password = $request->get('password');
		}
		$user->vendor_id = $request->get('vendor_id');
		$user->zip_code = $request->get('zip_code');
		$user->state = $request->get('state');

		$user->save();

		$user->roles()
			 ->sync([ $request->get('role') ]);

		return redirect(url('users'));
	}

	public function destroy ($id)
	{
		$user = User::where('is_deleted', 0)
					->find($id);
		if ( !$user ) {
			return view('errors.404');
		}

		$user->is_deleted = 1;
		$user->save();

		return redirect(url('users'));
	}
}
