<?php

namespace App\Http\Controllers;

use App\Models\User; // หรือ App\Models\Users หากเปลี่ยนชื่อ Model
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * เรียกดูข้อมูลผู้ใช้งานทั้งหมด พร้อมการแบ่งหน้าและการค้นหา
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10); // แบ่งหน้า หน้าละ 10 แถว

        return view('users.index', compact('users', 'search'));
    }

    /**
     * แสดงฟอร์มสำหรับเพิ่มผู้ใช้งานใหม่ (ไม่ได้อยู่ในขอบเขตของคำขอ แต่จำเป็นสำหรับ CRUD)
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * จัดเก็บผู้ใช้งานใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', Rule::in(['Patient', 'Staff', 'admin' ,'Doctor'])],
            'status' => ['required', Rule::in(['active', 'inactive', 'banned'])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * แสดงข้อมูลผู้ใช้งานรายบุคคล (View/Show)
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * แสดงฟอร์มสำหรับแก้ไขข้อมูลผู้ใช้งาน
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * อัปเดตข้อมูลผู้ใช้งาน
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['Patient', 'Staff', 'admin' ,'Doctor'])],
            'status' => ['required', Rule::in(['active', 'inactive', 'banned'])],
            'password' => 'nullable|string|min:6|confirmed', // อนุญาตให้ว่างถ้าไม่ต้องการเปลี่ยน
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * ลบผู้ใช้งาน
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}