<x-app-layout>
    <div style="display:grid; grid-template-columns:280px 1fr; gap:32px; padding:32px;">

        {{-- Left Categories --}}
        <div style="background:white; border:1px solid #e5e7eb; border-radius:16px; padding:24px;">
            <h2 style="font-size:16px; font-weight:600; margin-bottom:16px;">
                Categories
            </h2>

            <a href="{{ route('services.index') }}"
               style="display:flex; justify-content:space-between; padding:10px 14px; font-size:14px; border-radius:10px; text-decoration:none; color:#111; background:#f3f4f6; margin-bottom:8px;">
                <span>All categories</span>
                <span>{{ $categories->sum(fn($c) => $c->services->count()) + $uncategorizedServices->count() }}</span>
            </a>

            @foreach($categories as $category)
                <a href="{{ route('services.index', ['category' => $category->id]) }}"
                   style="display:flex; justify-content:space-between; padding:10px 14px; font-size:14px; border-radius:10px; text-decoration:none; color:#111; margin-bottom:8px; {{ request('category') == $category->id ? 'background:#eef2ff;' : '' }}">
                    <span>{{ $category->name }}</span>
                    <span>{{ $category->services->count() }}</span>
                </a>
            @endforeach
            <a href="{{ route('service-categories.create', ['return_url' => request()->fullUrl()]) }}"
            style="
                    display:block;
                    background:#111827;
                    color:white;
                    padding:8px 12px;
                    border-radius:8px;
                    text-decoration:none;
                    font-size:14px;
                    margin-bottom:14px;
                    text-align:center;
            ">
                + Add Category
            </a>
        </div>

        {{-- Right Services --}}
        <div>
            @php
                $selectedCategory = request('category')
                    ? $categories->firstWhere('id', request('category'))
                    : null;

                $servicesToShow = $selectedCategory
                    ? $selectedCategory->services
                    : $categories->flatMap->services->merge($uncategorizedServices);

                $title = $selectedCategory ? $selectedCategory->name : 'All Services';
            @endphp

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                <h1 style="font-size:18px; font-weight:600;">
                    {{ $title }}
                </h1>

                <a href="{{ route('services.create') }}"
                   style="background:#111827; color:white; padding:10px 18px; border-radius:999px; text-decoration:none;">
                    + Add Service
                </a>
            </div>

            @forelse($servicesToShow as $service)
                <div style="background:white; border:1px solid #e5e7eb; border-radius:16px; padding:16px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; border-left:10px solid #bae6fd;">
                    <div>
                        <h3 style="font-size:16px; font-weight:600; margin:0;">
                            {{ $service->name }}
                        </h3>

                        <div style="color:#6b7280; font-size:13px; margin-top:4px;">
                            {{ $service->duration }} min
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; gap:24px;">
                        <div style="font-size:16px; font-weight:600;">
                            CA$ {{ number_format($service->price, 0) }}
                        </div>

                        <div style="position:relative;">
                            <button type="button"
                                    onclick="toggleServiceMenu(event, {{ $service->id }})"
                                    style="
                                        background:none;
                                        border:none;
                                        font-size:22px;
                                        cursor:pointer;
                                        padding:4px 8px;
                                    ">
                                ⋮
                            </button>

                            <div id="service-menu-{{ $service->id }}"
                                class="service-action-menu"
                                style="
                                    display:none;
                                    position:absolute;
                                    right:0;
                                    top:32px;
                                    background:white;
                                    border:1px solid #e5e7eb;
                                    border-radius:8px;
                                    box-shadow:0 8px 20px rgba(0,0,0,0.12);
                                    min-width:150px;
                                    z-index:50;
                                    overflow:hidden;
                                ">

                                <a href="{{ route('services.edit', $service) }}"
                                style="
                                        display:block;
                                        padding:10px 14px;
                                        color:#111827;
                                        text-decoration:none;
                                        font-size:14px;
                                ">
                                    Edit Service
                                </a>

                                <form action="{{ route('services.destroy', $service) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            onclick="return confirm('Delete this service?')"
                                            style="
                                                display:block;
                                                width:100%;
                                                padding:10px 14px;
                                                background:white;
                                                border:none;
                                                color:#dc2626;
                                                text-align:left;
                                                font-size:14px;
                                                cursor:pointer;
                                            ">
                                        Delete Service
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="background:white; border-radius:16px; padding:32px; color:#666;">
                    No services found.
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function toggleServiceMenu(event, serviceId) {
            event.stopPropagation();

            document.querySelectorAll('.service-action-menu').forEach(menu => {
                if (menu.id !== 'service-menu-' + serviceId) {
                    menu.style.display = 'none';
                }
            });

            const menu = document.getElementById('service-menu-' + serviceId);

            if (menu.style.display === 'block') {
                menu.style.display = 'none';
            } else {
                menu.style.display = 'block';
            }
        }

        document.addEventListener('click', function () {
            document.querySelectorAll('.service-action-menu').forEach(menu => {
                menu.style.display = 'none';
            });
        });
    </script>    
</x-app-layout>
