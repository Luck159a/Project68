<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('แผงควบคุม (Dashboard)') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl mb-8">
                <div class="p-6 sm:p-8 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-2xl flex flex-col md:flex-row items-center justify-between">
                    <div class="mb-6 md:mb-0 text-center md:text-left">
                        <h3 class="text-2xl sm:text-3xl font-bold mb-2">ยินดีต้อนรับกลับ, {{ Auth::user()->name }}! 👋</h3>
                        <p class="text-indigo-100 text-base sm:text-lg">พร้อมที่จะจองคิวเพื่อเข้ารับบริการแล้วหรือยัง? เริ่มต้นได้ง่ายๆ ที่นี่</p>
                    </div>
                    <a href="{{ route('doctor-schedules.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-white text-indigo-600 font-bold rounded-full hover:bg-gray-100 transition shadow-md whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                        จองคิวแพทย์ทันที
                    </a>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                
                <div class="w-full lg:w-2/3 bg-white overflow-hidden shadow-sm sm:rounded-2xl flex flex-col">
                    <div class="p-6 sm:p-8 flex-1">
                        <h4 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            แนะนำวิธีการใช้งานระบบ (How It Works)
                        </h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center h-full">
                            <div class="p-6 bg-indigo-50 rounded-xl flex flex-col items-center">
                                <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mb-4 shadow-sm shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h5 class="font-bold text-lg mb-2 text-indigo-900 w-full">1. เลือกแพทย์และวันที่</h5>
                                <p class="text-gray-600 text-sm w-full">เข้าไปที่หน้า "จองคิว" เลือกแพทย์ที่ต้องการและวันที่ที่สะดวก</p>
                            </div>
                            
                            <div class="p-6 bg-purple-50 rounded-xl flex flex-col items-center">
                                <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mb-4 shadow-sm shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h5 class="font-bold text-lg mb-2 text-purple-900 w-full">2. จองช่วงเวลา</h5>
                                <p class="text-gray-600 text-sm w-full">เลือกช่วงเวลาที่ยัง "ว่าง" ระบุอาการเบื้องต้น และกดยืนยัน</p>
                            </div>
                            
                            <div class="p-6 bg-green-50 rounded-xl flex flex-col items-center">
                                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4 shadow-sm shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                </div>
                                <h5 class="font-bold text-lg mb-2 text-green-900 w-full">3. รับบัตรคิวและรอเรียก</h5>
                                <p class="text-gray-600 text-sm w-full">ระบบจะออกบัตรคิวให้ คุณสามารถตรวจสอบสถานะได้ที่หน้า "ประวัติการจอง"</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-1/3 bg-white overflow-hidden shadow-sm sm:rounded-2xl h-fit">
                    <div class="p-6 sm:p-8">
                        <h4 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            เมนูใช้งานบ่อย
                        </h4>
                        <div class="flex flex-col space-y-4">
                            <a href="{{ route('doctor-schedules.index') }}" 
                               class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-indigo-50 hover:text-indigo-700 transition group border border-transparent hover:border-indigo-100">
                                <div class="p-3 bg-white rounded-lg mr-4 shadow-sm group-hover:shadow-md transition shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-base sm:text-lg w-full">เริ่มจองคิวใหม่</span>
                            </a>
                            <a href="{{ route('queue.history') }}" 
                               class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-purple-50 hover:text-purple-700 transition group border border-transparent hover:border-purple-100">
                                <div class="p-3 bg-white rounded-lg mr-4 shadow-sm group-hover:shadow-md transition shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-base sm:text-lg w-full">ประวัติการจองของฉัน</span>
                            </a>
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 hover:text-gray-700 transition group border border-transparent hover:border-gray-200">
                                <div class="p-3 bg-white rounded-lg mr-4 shadow-sm group-hover:shadow-md transition shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-base sm:text-lg w-full">แก้ไขข้อมูลส่วนตัว</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>