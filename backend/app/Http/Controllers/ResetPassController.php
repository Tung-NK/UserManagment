<?php

namespace App\Http\Controllers;

// use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetPassController extends Controller
{
    public function sendMail(Request $request)
    {
        if (!$this->validateEmail($request->email)) {
            return $this->failedResponse();
        }
        $this->send($request->email);
        return response()->json(['error' => 'Success'], 200);
    }


    public function send($email)
    {
        $oldToken = DB::table('password_reset_tokens')->where('email', $email)->first();

        if ($oldToken) {
            return $oldToken;
        }

        $token  = Str::random(60);

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::send('email.passwordReset', ['token' => $token], function ($message) use ($email) {
            $message->to($email);
            $message->subject("Reset Password");
        });

        return $token;
    }


    public function validateEmail($email)
    {
        return !!User::where('email', $email)->first();
    }

    public function failedResponse()
    {
        return response()->json(['error' => 'Email không tồn tại'], 401);
    }


    public function resetPass(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'email' => "required|email|exists:users",
            'password' => "required|min:5|max:25",
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $resetData = DB::table('password_reset_tokens')
            ->where('email', $req->email)
            ->first();

        if (Carbon::parse($resetData->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $req->email)->delete();
            return response()->json(['message' => 'Token đã hết hạn'], 400);
        }

        User::where('email', $req->email)->update([
            'password' => Hash::make($req->password)
        ]);

        DB::table('password_reset_tokens')->where('email', $req->email)->delete();

        return response()->json(['message' => 'Mật khẩu đã được đặt lại thành công'], 200);
    }
}
