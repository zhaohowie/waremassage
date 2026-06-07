<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Staff Name</label>
    <input type="text"
           name="name"
           value="{{ old('name', $staff->name ?? '') }}"
           style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">

    @error('name')
        <div style="color:#dc2626; margin-top:4px;">{{ $message }}</div>
    @enderror
</div>

<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Email</label>
    <input type="email"
           name="email"
           value="{{ old('email', $staff->email ?? '') }}"
           style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">

    @error('email')
        <div style="color:#dc2626; margin-top:4px;">{{ $message }}</div>
    @enderror
</div>

<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Phone</label>
    <input type="text"
           name="phone"
           value="{{ old('phone', $staff->phone ?? '') }}"
           style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">
</div>

<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:8px;">Services Provided</label>

    <button type="button"
            onclick="openServicesModal()"
            style="background:#2563eb; color:white; padding:8px 16px; border-radius:6px;">
        + Add Services
    </button>

    <div id="selected-services-list" style="margin-top:12px; color:#374151;">
        Selected services will appear here.
    </div>
</div>

<div id="services-modal"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:9999;">
    <div style="background:white; width:90%; max-width:700px; margin:60px auto; padding:24px; border-radius:8px; max-height:80vh; overflow-y:auto;">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="font-size:20px; font-weight:bold;">Select Services</h3>

            <button type="button"
                    onclick="closeServicesModal()"
                    style="font-size:24px; background:none; border:none; cursor:pointer;">
                ×
            </button>
        </div>

        <input type="text"
               id="service-search"
               placeholder="Search services..."
               onkeyup="filterServices()"
               style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px; margin-bottom:16px;">

        <div id="services-checkbox-list">
            @foreach($services as $service)
                <label class="service-item"
                       data-name="{{ strtolower($service->name) }}"
                       style="display:block; padding:8px; border-bottom:1px solid #eee;">
                    <input type="checkbox"
                           name="services[]"
                           value="{{ $service->id }}"
                           data-label="{{ $service->name }}"
                           onchange="updateSelectedServices()"
                           style="margin-right:8px;"
                           {{ in_array($service->id, old('services', isset($staff) ? $staff->services->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                    {{ $service->name }} - ${{ number_format($service->price, 2) }} / {{ $service->duration }} min
                </label>
            @endforeach
        </div>

        <div style="margin-top:20px; text-align:right;">
            <button type="button"
                    onclick="closeServicesModal()"
                    style="background:#16a34a; color:white; padding:8px 16px; border-radius:6px;">
                Done
            </button>
        </div>
    </div>
</div>

<script>
    function openServicesModal() {
        document.getElementById('services-modal').style.display = 'block';
    }

    function closeServicesModal() {
        document.getElementById('services-modal').style.display = 'none';
    }

    function filterServices() {
        const search = document.getElementById('service-search').value.toLowerCase();
        const items = document.querySelectorAll('.service-item');

        items.forEach(item => {
            const name = item.getAttribute('data-name');
            item.style.display = name.includes(search) ? 'block' : 'none';
        });
    }

    function updateSelectedServices() {
        const checked = document.querySelectorAll('input[name="services[]"]:checked');
        const list = document.getElementById('selected-services-list');

        if (checked.length === 0) {
            list.innerHTML = 'Selected services will appear here.';
            return;
        }

        let html = '<strong>Selected:</strong><br>';

        checked.forEach(item => {
            html += '• ' + item.getAttribute('data-label') + '<br>';
        });

        list.innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', updateSelectedServices);
</script>

<div style="margin-bottom:16px;">
    <label style="display:flex; align-items:center;">
        <input type="checkbox"
               name="is_active"
               value="1"
               style="margin-right:8px;"
               {{ old('is_active', $staff->is_active ?? true) ? 'checked' : '' }}>
        Active
    </label>
</div>
