<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('ระบบจัดการคิวเข้ารับบริการ') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- ฟอร์มค้นหา --}}
                <form method="GET" action="{{ route('queues.index') }}" class="flex flex-wrap items-end gap-3 mb-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">ค้นหาเลขคิว/ชื่อคนไข้</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="ค้นหา..." 
                               class="mt-1 block w-64 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">เลือกวันที่เข้ารับบริการ</label>
                        <select name="date" class="mt-1 block w-48 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">วันที่ทั้งหมด</option>
                            @foreach($availableDates as $d)
                                <option value="{{ $d->schedule_date }}" {{ request('date') == $d->schedule_date ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($d->schedule_date)->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-700 transition shadow-sm font-medium">
                            ค้นหา
                        </button>

                        @if(request('date') || request('search'))
                            <a href="{{ route('queues.index') }}" class="text-sm text-gray-500 hover:text-red-600">ล้างค่า</a>
                        @endif
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">เลขคิว</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ช่วงเวลา</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">คนไข้</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">แพทย์</th> {{-- เพิ่มหัวข้อ --}}
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">สถานะ</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">การดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($queues as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-indigo-600 text-lg">
                                    {{ $item->labelNo }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $item->period }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item->user->name ?? 'ไม่พบชื่อคนไข้' }}
                                </td>

                                {{-- ⭐ ส่วนแสดงชื่อแพทย์ที่เพิ่มเข้ามา --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if(!$item->doctorSchedule)
                                        <span class="text-red-500 text-xs">Error: ไม่พบตารางเวร (ID: {{ $item->docschId }})</span>
                                    @elseif(!$item->doctorSchedule->user)
                                        <span class="text-orange-500 text-xs">Error: พบตารางเวร แต่ไม่พบชื่อหมอ</span>
                                    @else
                                        <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full border border-blue-200 text-xs font-semibold">
                                            {{ $item->doctorSchedule->user->name }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $colors = [
                                            'รอเรียก' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                            'กำลังใช้บริการ' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            'เสร็จสิ้น' => 'bg-green-100 text-green-700 border-green-200',
                                            'ยกเลิก' => 'bg-red-100 text-red-700 border-red-200',
                                        ];
                                        $style = $colors[$item->status] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $style }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex justify-center gap-2">
                                    {{-- ... ปุ่มดำเนินการเหมือนเดิม ... --}}
                                    @if($item->status === 'รอเรียก')
                                        <form action="{{ route('queues.updateStatus', $item->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="กำลังใช้บริการ">
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">เรียกคิว</button>
                                        </form>
                                        <form action="{{ route('queues.cancel', $item->id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการยกเลิกคิวนี้?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">ยกเลิกคิว</button>
                                        </form>
                                    @elseif($item->status === 'กำลังใช้บริการ')
                                        <form action="{{ route('queues.updateStatus', $item->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="เสร็จสิ้น">
                                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-xs font-semibold uppercase tracking-widest transition">เสร็จสิ้นงาน</button>
                                        </form>
                                    @elseif($item->status === 'ยกเลิก')
                                        <span class="text-red-500 font-bold italic">คิวนี้ถูกยกเลิกแล้ว</span>
                                    @else
                                        <span class="text-gray-400 italic text-sm">สิ้นสุดขั้นตอน</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    ไม่พบข้อมูลคิวตามเงื่อนไขที่ค้นหา
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $queues->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>