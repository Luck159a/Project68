<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ประวัติการจองคิวของคุณ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if($queues->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เลขคิว</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แพทย์</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ช่วงเวลา</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">การดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($queues as $q)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{-- ✅ แก้ไข: ใส่ลิงก์ครอบเลขคิวเพื่อให้กดไปดูหน้าลำดับคิวได้ --}}
                                            <a href="{{ route('queue.success', $q->id) }}" class="text-indigo-600 font-bold hover:text-indigo-900 hover:underline">
                                                {{ $q->labelNo }}
                                            </a>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $q->doctorSchedule->user->name ?? 'ไม่ระบุชื่อแพทย์' }}
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $q->doctorSchedule->schedule_date }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $q->period }} น.
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($q->status === 'เสร็จสิ้น')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    เสร็จสิ้น
                                                </span>
                                            @elseif($q->status === 'ยกเลิก' || $q->status === 'ยกเลิกคิว')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    ยกเลิก
                                                </span>
                                            @elseif($q->status === 'รอเรียก')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    รอเรียก
                                                </span>
                                            @else
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $q->status }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            {{-- ✅ เพิ่มปุ่มเพื่อให้ชัดเจนว่ากดดูรายละเอียดได้ --}}
                                            <a href="{{ route('queue.success', $q->id) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded-md transition">
                                                ดูรายละเอียด
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $queues->links() }}
                    </div>

                @else
                    <div class="text-center py-12">
                        <div class="flex justify-center">
                            <svg class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">ยังไม่มีประวัติการจอง</h3>
                        <p class="mt-2 text-sm text-gray-500">คุณยังไม่ได้ทำการจองคิวในระบบในขณะนี้</p>
                        <div class="mt-6">
                            <a href="{{ url('/doctor-schedules') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                ไปหน้าจองคิว
                            </a>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>