
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800"> User Management</h1>
        <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 shadow-md">
            + Add New User
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('users.index') }}" method="GET" class="mb-6">
        <div class="flex items-center space-x-2">
            <input type="text" name="search" placeholder="Search by Name, Email, or Role..."
                value="{{ $search ?? '' }}"
                class="flex-grow border border-gray-300 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm"
            >
            <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">
                Search 
            </button>
            @if($search)
                <a href="{{ route('users.index') }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">Clear</a>
            @endif
        </div>
    </form>

    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-100 text-left text-gray-600 uppercase text-sm">
                    <th class="px-5 py-3 border-b-2 border-gray-200">ID</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Name</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Email</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Role</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Status</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Created At</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-5 py-3 border-b border-gray-200 text-sm">{{ $user->id }}</td>
                        <td class="px-5 py-3 border-b border-gray-200 text-sm">{{ $user->name }}</td>
                        <td class="px-5 py-3 border-b border-gray-200 text-sm">{{ $user->email }}</td>
                        <td class="px-5 py-3 border-b border-gray-200 text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($user->role == 'admin') bg-red-100 text-red-800 @elseif($user->role == 'manager') bg-yellow-100 text-yellow-800 @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 border-b border-gray-200 text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($user->status == 'active') bg-blue-100 text-blue-800 @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 border-b border-gray-200 text-sm">{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="px-5 py-3 border-b border-gray-200 text-sm space-x-2">
                            
                            <a href="{{ route('users.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-900 font-medium">Edit</a>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
        {{-- Laravel จะสร้างลิงก์การแบ่งหน้าด้วย Tailwind CSS ให้อัตโนมัติ --}}
        
        {{-- หากต้องการลิงก์ที่มีการส่งค่าค้นหาติดไปด้วย --}}
        {{-- {{ $users->appends(['search' => $search])->links() }} --}}
    </div>

</div>
</x-app-layout>
