<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">First Name</label>
    <input type="text" name="first_name" value="{{ old('first_name', $customer->first_name ?? '') }}"
           style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">
</div>

<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Last Name</label>
    <input type="text" name="last_name" value="{{ old('last_name', $customer->last_name ?? '') }}"
           style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">
</div>

<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Gender</label>
    <select name="gender" style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">
        <option value="">Select gender</option>
        @foreach(['male', 'female', 'other', 'prefer_not_to_say'] as $gender)
            <option value="{{ $gender }}" {{ old('gender', $customer->gender ?? '') == $gender ? 'selected' : '' }}>
                {{ ucwords(str_replace('_', ' ', $gender)) }}
            </option>
        @endforeach
    </select>
</div>

<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Birthdate</label>
    <input type="date" name="birthdate" value="{{ old('birthdate', $customer->birthdate ?? '') }}"
           style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">
</div>

<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Email</label>
    <input type="email" name="email" value="{{ old('email', $customer->email ?? '') }}"
           style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">
</div>

<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Phone</label>
    <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
           style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">
</div>

<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Notes</label>
    <textarea name="notes" style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">{{ old('notes', $customer->notes ?? '') }}</textarea>
</div>
