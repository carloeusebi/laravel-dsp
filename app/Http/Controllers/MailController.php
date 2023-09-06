<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Http\Requests\ContactSupportRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactForm;
use App\Mail\ContactSupportMail;
use App\Mail\LinkToTest;
use App\Services\VerifaliaService;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public function sendFromForm(ContactFormRequest $request)
    {
        $data = $request->all();
        $emailTo = env('MAIL_TO');

        Mail::to($emailTo)->send(new ContactForm($data));
        return redirect()->back()->with('status', 'success');
    }


    public function sendEmailWithTestLink(Request $request)
    {
        $request->validate([
            'email_to' => 'required|email',
            'subject' => 'required',
            'link' => 'required|url'
        ]);
        $data = $request->all();
        $emailTo = env('MAIL_TO');

        Mail::to($emailTo)->send(new LinkToTest($data));
    }


    public function contactSupport(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'issue' => 'required'
        ]);
        $data = $request->all();
        $emailTo = env('MAIL_TO');

        Mail::to($emailTo)
            ->cc('carloeusebi@gmail.com')
            ->send(new ContactSupportMail($data));
    }
}
