<?php namespace App\Http\Controllers;

use App\EmailTemplate;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class EmailTemplateController extends Controller
{
	public function index ()
	{
		$templates = EmailTemplate::where('is_deleted', 0)
								  ->paginate(50);

		return view('email_templates.index')
			->withTemplates($templates)
			->with('count', 1);
	}

	public function create ()
	{
		abort(404);
	}

	public function store (Requests\EmailTemplateCreateRequest $request)
	{
		$email_template = new EmailTemplate();
		$email_template->message_type = trim($request->get('message_type'));
		$email_template->message_title = trim($request->get('message_title'));
		$email_template->save();

		return redirect()
			->back()
			->with('success', "Email template is saved successfully!");
	}

	public function show ($id)
	{
		return $this->edit($id);
	}

	public function edit ($id)
	{
		$template = EmailTemplate::find(intval($id));
		if ( !$template ) {
			abort(404);
		}

		return view('email_templates.edit')->with('template', $template);
	}

	public function update (Requests\EmailTemplateUpdateRequest $request, $id)
	{
		$email_template = EmailTemplate::find(intval($id));
		if ( !$email_template ) {
			abort(404);
		}
		$email_template->message_type = trim($request->get('message_type'));
		$email_template->message_title = trim($request->get('message_title'));
		$email_template->message = $request->get('message');
		$email_template->save();

		return redirect()
			->back()
			->with('success', 'Template is successfully updated');
	}

	public function destroy ($id)
	{
		$email_template = EmailTemplate::find($id);
		if ( !$email_template ) {
			abort(404);
		}
		$email_template->is_deleted = 1;
		$email_template->save();

		return redirect()
			->back()
			->with('success', "Email template is successfully deleted.");
	}
}
