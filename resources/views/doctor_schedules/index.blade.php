<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('ตารางเวลาแพทย์ & การจองคิว') }}
            </h2>
            
            {{-- ส่วนที่ 1: ปุ่มเพิ่มตารางเวลาสำหรับ Admin และ Staff --}}
            @if(in_array(Auth::user()->role, ['admin', 'staff']))
            <a href="{{ route('doctor-schedules.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                เพิ่มตารางเวลาใหม่
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- ส่วนแสดง Alert Success (ถ้ามี) --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- ระบบค้นหา --}}
                <form method="GET" action="{{ route('doctor-schedules.index') }}" class="mb-6 flex gap-4">
                    <input type="text" name="search" placeholder="ค้นหาชื่อหมอ..." 
                           class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 w-64"
                           value="{{ request('search') }}">
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition">
                        ค้นหา
                    </button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">แพทย์</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">วันที่ให้บริการ</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">ช่วงเวลา</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase text-center">สถานะ</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">ดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($schedules as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $item->user->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($item->schedule_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $item->start_time }} - {{ $item->end_time }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $item->status == 'available' ? 'เปิดรับจอง' : 'งดให้บริการ' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center items-center gap-3">
                                        {{-- ปุ่มจองคิว (สำหรับทุกคน) --}}
                                        @if($item->status == 'available')
                                        <a href="{{ route('queues.create', $item->id) }}" 
                                           class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition shadow-sm text-xs">
                                             จองคิวนี้
                                        </a>
                                        @endif

                                        {{-- ส่วนที่ 2: ปุ่มแก้ไข/ลบ สำหรับ Admin และ Staff --}}
                                        @if(in_array(Auth::user()->role, ['admin', 'staff']))
                                        <div class="flex items-center border-l pl-3 gap-3">
                                            <a href="{{ route('doctor-schedules.edit', $item) }}" class="text-amber-600 hover:text-amber-900 text-xs font-bold">แก้ไข</a>
                                            <form action="{{ route('doctor-schedules.destroy', $item) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-bold" onclick="return confirm('ยืนยันการลบตารางเวลานี้?')">
                                                    ลบ
                                                </button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                    ไม่พบข้อมูลตารางเวลาแพทย์ในขณะนี้
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $schedules->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>