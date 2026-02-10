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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form method="GET" action="{{ route('queues.index') }}" class="mb-6 flex gap-4">
                    <input type="text" name="search" placeholder="ค้นหาเลขคิว หรือชื่อคนไข้..." 
                           class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 w-64"
                           value="{{ request('search') }}">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition font-medium">
                        ค้นหา
                    </button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">เลขคิว</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ช่วงเวลา</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">คนไข้</th>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $item->user->name ?? 'ไม่พบชื่อผู้ใช้งาน' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $colors = [
                                            'รอเรียก' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                            'กำลังใช้บริการ' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            'เสร็จสิ้น' => 'bg-green-100 text-green-700 border-green-200'
                                        ];
                                        $style = $colors[$item->status] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $style }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <form action="{{ route('queues.updateStatus', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        
                                        @if($item->status === 'รอเรียก')
                                            <input type="hidden" name="status" value="กำลังใช้บริการ">
                                            <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded hover:bg-blue-700 text-sm font-medium transition inline-flex items-center">
                                                เรียกคิว
                                            </button>
                                        @elseif($item->status === 'กำลังใช้บริการ')
                                            <input type="hidden" name="status" value="เสร็จสิ้น">
                                            <button type="submit" class="bg-green-600 text-white px-4 py-1.5 rounded hover:bg-green-700 text-sm font-medium transition inline-flex items-center">
                                                เสร็จสิ้นงาน
                                            </button>
                                        @else
                                            <span class="text-gray-400 italic text-sm">สิ้นสุดขั้นตอน</span>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    ยังไม่มีคิวในรายการวันนี้
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $queues->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>