<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('จองคิวเข้ารับบริการ: หมอ') }} {{ $schedule->user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                
                <div class="flex items-center justify-between mb-8 border-b pb-4">
                    <div>
                        <p class="text-sm text-gray-500 uppercase tracking-wider">วันที่รับบริการ</p>
                        <p class="text-xl font-bold text-indigo-900">{{ $schedule->schedule_date }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500 uppercase tracking-wider">สถานะ</p>
                        <p class="text-green-600 font-bold">เปิดรับการจอง</p>
                    </div>
                </div>

                <form action="{{ route('queues.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="docschId" value="{{ $schedule->id }}">

                    <h3 class="font-bold text-lg mb-6 flex items-center">
                        เลือกช่วงเวลาที่ต้องการ
                    </h3>
               
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($slots as $slot)
            @if($slot['is_available'])
                <button type="button" 
                    onclick="selectTime(this, '{{ $slot['time'] }}')"
                    class="time-slot border-2 border-blue-500 text-blue-500 py-2 px-4 rounded-lg hover:bg-blue-50 transition-colors duration-200">
                    {{ $slot['time'] }}
                </button>
            @else
                <button type="button" disabled 
                    class="bg-gray-200 text-gray-400 py-2 px-4 rounded-lg cursor-not-allowed">
                    {{ $slot['time'] }} (เต็ม)
                </button>
            @endif
        @endforeach
    </div>

    @error('period')
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
    @enderror

                    <div class="space-y-4 mb-8">
                        @foreach($slots as $slot)
                            <div class="flex items-center justify-between p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                <span class="text-lg font-semibold text-gray-700">{{ $slot['time'] }}</span>

                                <label class="relative inline-block">
                                    @if($slot['is_available'])
                                        <input type="radio" name="period" value="{{ $slot['time'] }}" class="peer hidden" required>
                                        
                                        <div class="cursor-pointer py-2 px-6 rounded-md font-medium text-sm transition-all
                                                    bg-blue-500 text-white border border-blue-600
                                                    hover:bg-blue-600
                                                    peer-checked:bg-green-600 peer-checked:border-green-700 shadow-sm"
                                                    onclick="selectTime(this, '{{ $slot['time'] }}')">
                                            ว่าง

                                        </div>
                                            
                                    @else
                                        <div class="py-2 px-6 rounded-md font-medium text-sm
                                                    bg-red-500 text-white border border-red-600 opacity-60 cursor-not-allowed">
                                            เต็มแล้ว
                                        </div>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-8">
                        <label class="block font-bold text-gray-700 mb-2">หมายเหตุ (ระบุอาการเบื้องต้น)</label>
                        <textarea name="Note" rows="3" 
                            placeholder="ระบุอาการเบื้องต้น..."
                            class="w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all"></textarea>
                    </div>

                    <div class="flex items-center justify-end border-t pt-6">
                        <button type="submit" class="bg-blue-600 text-white px-10 py-3 rounded-lg font-bold hover:bg-blue-700 transition-all shadow-lg">
                            ยืนยันการจองคิว
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <script>
function selectTime(element, time) {
    // 1. ล้างสีปุ่มอื่นๆ ให้กลับเป็นสีฟ้าขาวเหมือนเดิม (Tailwind Classes)
    const allButtons = document.querySelectorAll('.time-slot');
    allButtons.forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white'); // เอาสีที่เลือกออก
        btn.classList.add('border-blue-500', 'text-blue-500'); // ใส่สีเดิมกลับไป
    });

    // 2. ไฮไลท์ปุ่มที่ถูกคลิก (เปลี่ยนเป็นสีเข้ม)
    element.classList.remove('border-blue-500', 'text-blue-500');
    element.classList.add('bg-blue-600', 'text-white');

    // 3. เอาค่าเวลาไปใส่ใน Hidden Input เพื่อส่งเข้า Controller
    document.getElementById('selected_period').value = time;
}
</script>
</x-app-layout>