<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{

    public function __construct()
    {
        // 中间件过滤 除了show方法 都用中间件过滤 意味着没有通过认证的用户无法访问 edit 和 update 方法
        $this->middleware('auth',['except' => 'show']);
    }

    public function show(User $user) {
        return view('users.show',compact('user'));
    }

    public function edit(User $user) {
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    public function update(UserRequest $userRequest,ImageUploadHandler $imageUploadHandler,User $user) {
        $this->authorize('update',$user);
        $data = $userRequest->all();

        if ($userRequest->avatar) {
            $result = $imageUploadHandler->save($userRequest->avatar,'avatar',$user->id,416);
            if ($result) {
                $data['avatar'] = $result['path'];
            }
        }
        $user->update($data);
        return redirect()->route('users.show',$user->id)->with('success','个人资料更新成功!');
    }
}
