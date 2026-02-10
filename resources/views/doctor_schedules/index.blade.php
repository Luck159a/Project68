<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('จัดการตารางเวลาหมอ') }}
            </h2>
            <a href="{{ route('doctor-schedules.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                เพิ่มตารางเวลาใหม่
            </a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form method="GET" class="mb-6 flex gap-4">
                    <input type="text" name="search" placeholder="ค้นหาชื่อหมอ..." 
                           class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500"
                           value="{{ request('search') }}">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">ค้นหา</button>
                </form>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">หมอ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">วันที่</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">เวลา</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">สถานะ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($schedules as $item)
                        <tr>
                            <td class="px-6 py-4">{{ $item->user->name }}</td>
                            <td class="px-6 py-4">{{ $item->schedule_date }}</td>
                            <td class="px-6 py-4">{{ $item->start_time }} - {{ $item->end_time }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-sm {{ $item->status == 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 flex gap-2">
                                <a href="{{ route('doctor-schedules.edit', $item) }}" class="text-indigo-600">แก้ไข</a>
                                <form action="{{ route('doctor-schedules.destroy', $item) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600" onclick="return confirm('ยืนยัน?')">ลบ</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $schedules->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>