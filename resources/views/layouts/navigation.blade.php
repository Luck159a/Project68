<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        {{-- 1. เมนูหน้าแรก (Dashboard) --}}
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('หน้าแรก') }}
                        </x-nav-link>

                        {{-- 2. เมนูเฉพาะ ADMIN เท่านั้น --}}
                        @if (strtolower(auth()->user()->role) === 'admin')
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                {{ __('จัดการข้อมูลผู้ใช้') }}
                            </x-nav-link>
                        @endif

                        {{-- 3. เมนูสำหรับ STAFF และ ADMIN (จัดการตารางหมอและคิว) --}}
                        @if (in_array(strtolower(auth()->user()->role), ['admin', 'staff']))
                            <x-nav-link :href="route('doctor-schedules.index')" :active="request()->routeIs('doctor-schedules.*')">
                                {{ __('จัดการตารางการทำงานของหมอ') }}
                            </x-nav-link>

                            <x-nav-link :href="route('queues.index')" :active="request()->routeIs('queues.*')">
                                {{ __('จัดการคิวเข้ารับบริการ') }}
                            </x-nav-link>

                            {{-- ⭐ 4. เมนูรายงาน (Dropdown) --}}
                            <div class="hidden sm:flex sm:items-center sm:ms-4">
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                            <div>รายงาน</div>
                                            <div class="ms-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        {{-- แสดง "รายงานบัญชีผู้ใช้" เฉพาะ Admin เท่านั้น --}}
                                        @if (strtolower(auth()->user()->role) === 'admin')
                                            <x-dropdown-link href="{{ route('reports.users.pdf') }}">
                                                {{ __('รายงานบัญชีผู้ใช้ (PDF)') }}
                                            </x-dropdown-link>
                                        @endif

                                        {{-- รายงานผู้เข้ารับบริการ ให้เห็นทั้ง Admin และ Staff --}}
                                        <x-dropdown-link href="{{ route('queues.export-pdf', request()->query()) }}" target="_blank">
                                            {{ __('รายงานผู้เข้ารับบริการ (PDF)') }}
                                        </x-dropdown-link>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        @endif

                        {{-- 5. เมนูเฉพาะ PATIENT --}}
                        @if (strtolower(auth()->user()->role) === 'patient')
                            <x-nav-link :href="route('doctor-schedules.index')" :active="request()->routeIs('doctor-schedules.index')">
                                จองคิว
                            </x-nav-link>
                            <x-nav-link :href="route('queue.history')" :active="request()->routeIs('queue.history')">
                                ประวัติการจอง
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Profile Dropdown --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center">
                                {{ Auth::user()->name }}
                                <span class="ml-2 px-2 py-1 text-xs font-bold text-white rounded-full {{ strtolower(Auth::user()->role) === 'admin' ? 'bg-red-600' : (strtolower(Auth::user()->role) === 'staff' ? 'bg-blue-600' : 'bg-green-600') }}">
                                    {{ strtoupper(Auth::user()->role) }}
                                </span>
                            </div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">โปรไฟล์</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">ออกจากระบบ</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>