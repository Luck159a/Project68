<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ระบบวิเคราะห์ข้อมูลผู้ใช้งาน') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-10">
                <h3 class="text-xl font-extrabold text-gray-900 mb-6 flex items-center">
                    <span class="bg-indigo-600 w-2 h-7 mr-3 rounded-full"></span>
                    📊 สถิติผู้สมัครใหม่วันนี้
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white border-2 border-orange-500 rounded-3xl p-6 shadow-md transform transition hover:scale-105">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-orange-600 font-black uppercase tracking-widest text-xs">แพทย์ (ใหม่)</p>
                                <p class="text-gray-900 text-6xl font-black mt-4">
                                    {{ $stats['daily']['doctor'] }} 
                                    <span class="text-2xl text-gray-400 font-bold">คน</span>
                                </p>
                            </div>
                            <div class="bg-orange-500 p-3 rounded-2xl shadow-lg shadow-orange-200">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border-2 border-blue-500 rounded-3xl p-6 shadow-md transform transition hover:scale-105">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-600 font-black uppercase tracking-widest text-xs">เจ้าหน้าที่ (ใหม่)</p>
                                <p class="text-gray-900 text-6xl font-black mt-4">
                                    {{ $stats['daily']['staff'] }} 
                                    <span class="text-2xl text-gray-400 font-bold">คน</span>
                                </p>
                            </div>
                            <div class="bg-blue-600 p-3 rounded-2xl shadow-lg shadow-blue-200">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border-2 border-green-500 rounded-3xl p-6 shadow-md transform transition hover:scale-105">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-green-600 font-black uppercase tracking-widest text-xs">คนไข้ (ใหม่)</p>
                                <p class="text-gray-900 text-6xl font-black mt-4">
                                    {{ $stats['daily']['patient'] }} 
                                    <span class="text-2xl text-gray-400 font-bold">คน</span>
                                </p>
                            </div>
                            <div class="bg-green-600 p-3 rounded-2xl shadow-lg shadow-green-200">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-12 border-gray-200">

            <div>
                <h3 class="text-xl font-extrabold text-gray-900 mb-6 flex items-center">
                    <span class="bg-indigo-600 w-2 h-7 mr-3 rounded-full"></span>
                    📅 สรุปยอดสะสมเดือนนี้
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 border-b-8 border-b-orange-500">
                        <p class="text-gray-400 font-bold text-xs uppercase tracking-widest">Doctors ทั้งหมด</p>
                        <p class="text-5xl font-black text-gray-900 mt-3">{{ $stats['monthly']['doctor'] }} <span class="text-xl font-bold text-gray-300">ราย</span></p>
                    </div>
                    
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 border-b-8 border-b-blue-500">
                        <p class="text-gray-400 font-bold text-xs uppercase tracking-widest">Staff ทั้งหมด</p>
                        <p class="text-5xl font-black text-gray-900 mt-3">{{ $stats['monthly']['staff'] }} <span class="text-xl font-bold text-gray-300">ราย</span></p>
                    </div>

                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 border-b-8 border-b-green-500">
                        <p class="text-gray-400 font-bold text-xs uppercase tracking-widest">Patient ทั้งหมด</p>
                        <p class="text-5xl font-black text-gray-900 mt-3">{{ $stats['monthly']['patient'] }} <span class="text-xl font-bold text-gray-300">ราย</span></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>