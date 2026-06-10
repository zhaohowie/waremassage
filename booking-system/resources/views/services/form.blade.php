<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Category</label>

    <select name="service_category_id"
            style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">
        <option value="">No Category</option>

        @foreach($categories as $category)
            <option value="{{ $category->id }}"
                {{ old('service_category_id', $service->service_category_id ?? '') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    @error('service_category_id')
        <div style="color:#dc2626; margin-top:4px;">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label class="block mb-1">Service Name</label>
    <input type="text" name="name" class="w-full border-gray-300 rounded" value="{{ old('name', $service->name ?? '') }}">
    @error('name') <div class="text-red-600">{{ $message }}</div> @enderror
</div>

<div class="mb-4">
    <label class="block mb-1">Description</label>
    <textarea name="description" class="w-full border-gray-300 rounded">{{ old('description', $service->description ?? '') }}</textarea>
</div>

<div class="mb-4">
    <label class="block mb-1">Price</label>
    <input type="number" step="0.01" name="price" class="w-full border-gray-300 rounded" value="{{ old('price', $service->price ?? '') }}">
    @error('price') <div class="text-red-600">{{ $message }}</div> @enderror
</div>

<div class="mb-4">
    <label class="block mb-1">Duration in minutes</label>
    <input type="number" name="duration" class="w-full border-gray-300 rounded" value="{{ old('duration', $service->duration ?? '') }}">
    @error('duration') <div class="text-red-600">{{ $message }}</div> @enderror
</div>

<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Cleanup Time in minutes</label>
    <input type="number"
           name="cleanup_time"
           min="0"
           value="{{ old('cleanup_time', $service->cleanup_time ?? 0) }}"
           style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">
</div>

<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Service Color</label>

    <input type="color"
           name="color"
           value="{{ old('color', $service->color ?? '#3b82f6') }}"
           style="width:80px; height:40px;">
</div>

<div class="mb-4">
    <label class="flex items-center">
        <input type="checkbox" name="is_active" value="1" class="mr-2"
            {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }}>
        Active
    </label>
</div>