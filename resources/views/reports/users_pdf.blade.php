<!DOCTYPE html>
<html lang="th">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* 1. โหลดฟอนต์ Sarabun จากโฟลเดอร์ public/fonts */
        @font-face {
            font-family: 'Sarabun';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/Sarabun-Thin.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'Sarabun';
            font-style: normal;
            font-weight: bold;
            src: url("{{ public_path('fonts/Sarabun-Bold.ttf') }}") format('truetype');
        }

        /* 2. ตั้งค่าพื้นฐาน ให้บังคับใช้ฟอนต์ Sarabun ทุกจุด */
        body {
            font-family: 'Sarabun', sans-serif;
            font-size: 16px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h2 {
            font-family: 'Sarabun', sans-serif;
            font-weight: bold;
            text-align: center;
            font-size: 24px;
            margin-top: 0;
        }

        .report-info {
            font-family: 'Sarabun', sans-serif;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* 3. จัดการตารางและการแสดงผลภาษาไทยในช่อง */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* ช่วยควบคุมความกว้างของคอลัมน์ */
        }

        th {
            font-family: 'Sarabun', sans-serif;
            font-weight: bold;
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 10px 5px;
            text-align: center;
        }

        td {
            font-family: 'Sarabun', sans-serif;
            border: 1px solid #000;
            padding: 8px 5px;
            word-wrap: break-word; /* ป้องกันข้อความยาวล้นช่อง */
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>รายงานบัญชีผู้ใช้</h2>
    
    <div class="report-info">
        <strong>วันที่ออกรายงาน:</strong> {{ date('d/m/Y') }} <br>
        <strong>จำนวนผู้ใช้ทั้งหมด:</strong> {{ count($users) }} ราย
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="10%">ID</th>
                <th width="40%">ชื่อ-นามสกุล</th>
                <th width="35%">อีเมล</th>
                <th width="15%">บทบาท</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td class="text-center">{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td class="text-center">{{ ucfirst($user->role) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">ไม่พบข้อมูลผู้ใช้</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>