<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Subjective</label>
    <textarea name="subjective" rows="4"
              style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">{{ old('subjective', $soapNote->subjective ?? '') }}</textarea>
</div>

<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Objective</label>
    <textarea name="objective" rows="4"
              style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">{{ old('objective', $soapNote->objective ?? '') }}</textarea>
</div>

<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Assessment</label>
    <textarea name="assessment" rows="4"
              style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">{{ old('assessment', $soapNote->assessment ?? '') }}</textarea>
</div>

<div style="margin-bottom:16px;">
    <label style="display:block; margin-bottom:6px;">Plan</label>
    <textarea name="plan" rows="4"
              style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:8px;">{{ old('plan', $soapNote->plan ?? '') }}</textarea>
</div>
