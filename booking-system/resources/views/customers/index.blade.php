<x-app-layout>
    <x-slot name="header">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Customers
            </h2>

            <a href="{{ route('customers.create') }}"
               style="background:#2563eb; color:white; padding:8px 16px; border-radius:6px; text-decoration:none;">
                + Add Customer
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div style="background:#dcfce7; color:#166534; padding:12px; border-radius:6px; margin-bottom:20px;">
                {{ session('success') }}
            </div>
        @endif

        <div style="background:white; border-radius:8px; padding:24px;">
            <form method="GET" action="{{ route('customers.index') }}"
                style="display:flex; gap:10px; margin-bottom:20px;">

                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search first name, last name, or phone..."
                    style="flex:1; border:1px solid #d1d5db; border-radius:6px; padding:8px;">

                <button type="submit"
                        style="background:#2563eb; color:white; padding:8px 16px; border-radius:6px;">
                    Search
                </button>

                <a href="{{ route('customers.index') }}"
                style="background:#6b7280; color:white; padding:8px 16px; border-radius:6px; text-decoration:none;">
                    Clear
                </a>
            </form>             
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #ddd;">
                        <th style="text-align:left; padding:12px;">Name</th>
                        <th style="text-align:left; padding:12px;">Gender</th>
                        <th style="text-align:left; padding:12px;">Birthdate</th>
                        <th style="text-align:left; padding:12px;">Email</th>
                        <th style="text-align:left; padding:12px;">Phone</th>
                        <th style="text-align:right; padding:12px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($customers as $customer)
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:12px;">{{ $customer->full_name }}</td>
                            <td style="padding:12px;">{{ $customer->gender ?? '-' }}</td>
                            <td style="padding:12px;">{{ $customer->birthdate ?? '-' }}</td>
                            <td style="padding:12px;">{{ $customer->email ?? '-' }}</td>
                            <td style="padding:12px;">{{ $customer->phone ?? '-' }}</td>
                            <td style="padding:12px; text-align:right;">
                                <a href="{{ route('customers.edit', $customer) }}" style="color:#2563eb;">Edit</a>

                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display:inline-block; margin-left:12px;">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            onclick="return confirm('Delete this customer?')"
                                            style="background:none; border:none; color:#dc2626; cursor:pointer;">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:20px; text-align:center; color:#666;">
                                No customers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
