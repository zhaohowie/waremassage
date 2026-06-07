<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Category Name</label>

    <input type="text"
           name="name"
           value="{{ old('name', $serviceCategory->name ?? '') }}"
           style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">

    @error('name')
        <div style="color:#dc2626; margin-top:4px;">{{ $message }}</div>
    @enderror
</div>

<div style="margin-bottom:16px;">
    <label style="display:flex; align-items:center;">
        <input type="checkbox"
               name="is_active"
               value="1"
               style="margin-right:8px;"
               {{ old('is_active', $serviceCategory->is_active ?? true) ? 'checked' : '' }}>
        Active
    </label>
</div>
