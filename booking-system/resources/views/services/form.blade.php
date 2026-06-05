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

<div class="mb-4">
    <label class="flex items-center">
        <input type="checkbox" name="is_active" value="1" class="mr-2"
            {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }}>
        Active
    </label>
</div>