<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function getAllUser()
    {
        $dataUser = User::select('id', 'name', 'email', 'created_at', 'image', 'account_status', 'role')->get();

        return response()->json([
            'status' => 'success',
            'data' => $dataUser
        ]);
    }

    public function store(Request $request)
    {
        // Xác thực dữ liệu
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'image' => 'nullable|string',
            'phone' => 'nullable|string',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'genre' => 'nullable|string',
            'nationality' => 'nullable|string',
            'job_status' => 'nullable|string',
            'occupation' => 'nullable|string',
            'hobbies' => 'nullable|string',
            'role' => 'required',
            'account_status' => 'nullable|string',
        ]);

        // Tạo người dùng mới
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'password' => Hash::make($validatedData['password']),
            'image' => $validatedData['image'] ?? null,
            'phone' => $validatedData['phone'] ?? null,
            'description' => $validatedData['description'] ?? null,
            'address' => $validatedData['address'] ?? null,
            'birth_date' => $validatedData['birth_date'] ?? null,
            'genre' => $validatedData['genre'] ?? null,
            'nationality' => $validatedData['nationality'] ?? null,
            'job_status' => $validatedData['job_status'] ?? null,
            'occupation' => $validatedData['occupation'] ?? null,
            'hobbies' => $validatedData['hobbies'] ?? null,
            'account_status' => $validatedData['account_status'] ?? 'active',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Xác thực dữ liệu
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'phone' => 'nullable|string',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'genre' => 'nullable|string',
            'nationality' => 'nullable|string',
            'job_status' => 'nullable|string',
            'occupation' => 'nullable|string',
            'hobbies' => 'nullable|string',
            'account_status' => 'nullable|string',
        ]);

        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        // Xử lý ảnh (nếu có)
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu cần
            if ($user->image && Storage::exists($user->image)) {
                Storage::delete($user->image);
            }

            // Lưu ảnh mới
            $imagePath = $request->file('image')->store('uploads', 'public');
            $validatedData['image'] = $imagePath;
        }

        // Cập nhật dữ liệu
        $user->update(array_filter($validatedData));

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    public function destroy($id)
    {
        // Tìm người dùng theo ID
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        // Xóa người dùng
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully',
        ]);
    }
}
