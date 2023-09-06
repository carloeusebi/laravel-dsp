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


    // public function sendEmailWithTestLink(Request $request)
    // {
    //     $data = $request->all();
    //     $emailTo = env('MAIL_TO');

    //     Mail::to($emailTo)->send(new LinkToTest($data));
    // }


    // public function contactSupport(ContactSupportRequest $request)
    // {
    //     $data = $request->all();
    //     $emailTo = env('MAIL_TO');

    //     Mail::to($emailTo)
    //         ->cc('carloeusebi@gmail.com')
    //         ->send(new ContactSupportMail($data));
    // }
}
