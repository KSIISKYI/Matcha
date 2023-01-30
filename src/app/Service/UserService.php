<?php

namespace App\Service;

use Rakit\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use DateTime;

use App\Models\{User, PendingUser, NewUserEmail};
use DI\Container;

class UserService
{
    static function validateRegisterForm(Validator $validator, array $data)
    {
        $validation = $validator->make($data, [
            'username' => 'required|min:6|max:15|usernameAvailable|alpha_dash',
            'email' => 'required|email|emailAvailable',
            'password' => 'required|min:8|alpha_dash',
            'confirm_password' => 'required|same:password'
        ]);

        $validation->validate();

        if ($validation->fails()) {
            return $validation->errors()->toArray();
        }
    }

    static function validateLoginForm($data)
    {
        $user = User::where('username', $data['username'])->first();
        $res = [
            'user' => $user,
            'error' => null
        ];

        if (!($user && password_verify($data['password'], $user->password))) {
            $res['error'] = 'Invalid username or password';
        } elseif (!$user->is_active) {
            $res['error'] = 'We have sent you an email with information about mail verification';
        }

        return $res;
    }

    static function validateAccountSettingsForm(Validator $validator, Container $container,  array $data)
    {
        $user = $container->get('user');
        $errors = [];

        // if email and password changed
        if ($user->email !== $data['email'] AND (!empty($data['old_password']) OR !empty($data['new_password']) OR !empty($data['confirm_new_password']))) {
            $validation = $validator->make($data, [
                'email' => 'required|email|emailAvailable',
                'old_password' => 'required|checkOldPassword',
                'new_password' => 'required|min:8|alpha_dash',
                'confirm_new_password' => 'required|same:new_password'
            ]);

            $validation->validate();

            if ($validation->fails()) {
                $errors = array_merge($validation->errors()->toArray(), $errors);
            }
        } else {
            // if only email chenged
            if ($user->email !== $data['email']) {
                $validation = $validator->make($data, [
                    'email' => 'required|email|emailAvailable'
                ]);

                $validation->validate();
    
                if ($validation->fails()) {
                    $errors = array_merge($validation->errors()->toArray(), $errors);
                }
    
            }

            //if only password changed
            if(!empty($data['old_password']) || !empty($data['new_password']) || !empty($data['confirm_new_password'])) {
                $validation = $validator->make($data, [
                    'old_password' => 'required|checkOldPassword',
                    'new_password' => 'required|min:8|alpha_dash',
                    'confirm_new_password' => 'required|same:new_password'
                ]);
    
                $validation->validate();
    
                if ($validation->fails()) {
                    $errors = array_merge($validation->errors()->toArray(), $errors);
                }
            } 

        }

        return $errors;
    }

    static function createUser($data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $user = User::create($data);
        ProfileService::createProfile($user);
        
        return $user;
    }

    static function createPendingUser(Model $user)
    {
        $pending_user = PendingUser::create([
            'token' => sha1(uniqid(time(), true)),
            'user_id' => $user->id
        ]);

        return $pending_user;
    }

    static function registerUser($user_data)
    {
        $user = self::createUser($user_data);
        self::createPendingUser($user);
        MailService::sendActivationMail($user);
    }

    static function loginUser($user)
    {
        $_SESSION['user'] = $user->id;
    }

    static function activateUser($data)
    {
        if (isset($data['activation_token']) && PendingUser::where('token', $data['activation_token'])->count() > 0) {
            $pending_user = PendingUser::where('token', $data['activation_token'])->first();
            $user = $pending_user->user;
            $user->is_active = true;
            $user->save();
            $pending_user->delete();

            return true;
        } 

        return false;
    }

    static function changePassword(User $user, $new_password)
    {
        $user->password = password_hash($new_password, PASSWORD_DEFAULT);
        $user->save();
    }

    static function resetPassword(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        $new_pass = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
        MailService::sendRecoveryMail($user, $new_pass);
        self::changePassword($user, $new_pass);
    }

    static function createNewEmailUser($user, $new_email)
    {
        return NewUserEmail::create([
            'token' => sha1(uniqid(time(), true)),
            'email' => $new_email,
            'user_id' => $user->id
        ]);
    }

    static function changeEmail(array $data)
    {
        $new_user_email = NewUserEmail::where('token', '=', $data['activation_token'])
            ->where('created_at', '>', date("Y-m-d H:i:s", time() - 86400))
            ->first();

        if( $new_user_email) {
            $new_email = $new_user_email->email;
            $user =  $new_user_email->user;
    
            $user->email = $new_email;
            $user->save();
            $new_user_email->delete();

            return true;
        }

        return false;
    }

    static function updateUser(User $user, array $data)
    {
        $user->update($data);
        $user->save();
    }
}
