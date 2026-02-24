<?php

namespace App\Http\Controllers;

use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf; // นำมาไว้ด้านบนสุด

class UserController extends Controller
{
    /**
     * เรียกดูข้อมูลผู้ใช้งานทั้งหมด พร้อมการแบ่งหน้าและการค้นหา
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::when($search, function ($query, $search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10); 

        return view('users.index', compact('users', 'search'));
    }

    /**
     * ส่งออกไฟล์ PDF พร้อมระบบ Filter ข้อมูล
     */
    public function exportPDF(Request $request)
    {
        // 1. รับค่าการค้นหาจาก URL (เหมือนหน้า index)
        $search = $request->query('search');

        // 2. กรองข้อมูลตามคำค้นหา
        $users = User::when($search, function ($query, $search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        })
        ->orderBy('id', 'asc')
        ->get();

        $totalUsers = $users->count();

        // 3. สร้าง PDF โดยใช้ View 'reports.users_pdf'
        $pdf = Pdf::loadView('reports.users_pdf', compact('users', 'search', 'totalUsers'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'Sarabun' // ตรวจสอบว่าในไฟล์ PDF ใช้ Font ชื่อนี้
            ]);

        return $pdf->stream('user-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * ส่วนของ CRUD อื่นๆ (คงเดิมไว้)
     */
    public function create()
    {
        return view('users.create');
    }

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

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['Patient', 'Staff', 'admin' ,'Doctor'])],
            'status' => ['required', Rule::in(['active', 'inactive', 'banned'])],
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $user->name; // ลอจิกการอัปเดตตามที่คุณเขียนไว้
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}