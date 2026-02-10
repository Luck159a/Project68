<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('จองคิวเข้ารับบริการ: หมอ') }} {{ $schedule->user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <p class="mb-6 text-gray-600">วันที่รับบริการ: **{{ $schedule->schedule_date }}**</p>

                <form action="{{ route('queues.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="docschId" value="{{ $schedule->id }}">

                    <h3 class="font-bold text-lg mb-4">เลือกช่วงเวลาที่ต้องการ</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        @foreach($slots as $slot)
                            <label class="relative">
                                @if($slot['is_available'])
                                    <input type="radio" name="period" value="{{ $slot['time'] }}" class="peer hidden" required>
                                    <div class="border-2 rounded-xl p-4 text-center cursor-pointer transition-all 
                                                peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600
                                                hover:border-indigo-400 hover:bg-indigo-50">
                                        <span class="block font-bold">{{ $slot['time'] }}</span>
                                        <span class="text-xs">ว่าง</span>
                                    </div>
                                @else
                                    <div class="border-2 border-gray-100 bg-gray-100 rounded-xl p-4 text-center cursor-not-allowed opacity-50">
                                        <span class="block font-bold text-gray-400">{{ $slot['time'] }}</span>
                                        <span class="text-xs text-gray-400">เต็มแล้ว</span>
                                    </div>
                                @endif
                            </label>
                        @endforeach
                    </div>

                    <div class="mb-6">
                        <label class="block font-medium text-gray-700">หมายเหตุ (ระบุอาการเบื้องต้น)</label>
                        <textarea name="Note" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 mt-1"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-700 transition shadow-lg">
                            ยืนยันการจองคิว
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>