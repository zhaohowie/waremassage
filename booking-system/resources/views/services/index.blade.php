```php
<x-app-layout>

    <x-slot name="header">
        <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Services
            </h2>

            <a href="{{ route('services.create') }}"
               style="background:#2563eb;
                      color:#ffffff;
                      padding:8px 16px;
                      border-radius:6px;
                      text-decoration:none;">
                + Add Service
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div style="background:#dcfce7;
                        color:#166534;
                        padding:12px;
                        border-radius:6px;
                        margin-bottom:20px;">
                {{ session('success') }}
            </div>
        @endif

        <div style="background:white;
                    border-radius:8px;
                    padding:24px;
                    box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #ddd;">
                        <th style="text-align:left; padding:12px;">Name</th>
                        <th style="text-align:left; padding:12px;">Category</th>
                        <th style="text-align:left; padding:12px;">Price</th>
                        <th style="text-align:left; padding:12px;">Duration</th>
                        <th style="text-align:left; padding:12px;">Status</th>
                        <th style="text-align:right; padding:12px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:12px;">
                                {{ $service->name }}
                            </td>
                            <td style="padding:12px;">
                                {{ $service->category->name ?? 'No Category' }}
                            </td>
                            <td style="padding:12px;">
                                ${{ number_format($service->price, 2) }}
                            </td>
                            <td style="padding:12px;">
                                {{ $service->duration }} min
                            </td>
                            <td style="padding:12px;">
                                {{ $service->is_active ? 'Active' : 'Inactive' }}
                            </td>
                            <td style="padding:12px; text-align:right;">
                                <a href="{{ route('services.edit', $service) }}"
                                   style="color:#2563eb; text-decoration:none;">
                                    Edit
                                </a>
                                <form action="{{ route('services.destroy', $service) }}"
                                      method="POST"
                                      style="display:inline-block; margin-left:12px;">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            onclick="return confirm('Delete this service?')"
                                            style="background:none;
                                                   border:none;
                                                   color:#dc2626;
                                                   cursor:pointer;">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"
                                style="padding:20px;
                                       text-align:center;
                                       color:#666;">
                                No services found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

