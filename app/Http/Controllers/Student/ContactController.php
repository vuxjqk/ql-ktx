<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function create()
    {
        return view('student.contact');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $recipients = [
            '2001225912@huit.edu.vn',
            'clintonvu123@gmail.com',
            'trananhduc6996@gmail.com',
        ];

        foreach ($recipients as $email) {
            Mail::to($email)->send(new ContactMail($data));
        }

        return redirect()->back()->with('success', 'Cảm ơn bạn! Tin nhắn đã được gửi thành công, chúng tôi sẽ phản hồi sớm nhất!');
    }
}
