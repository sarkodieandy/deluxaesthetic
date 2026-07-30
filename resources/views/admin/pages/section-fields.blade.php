<article class="border border-[var(--admin-border)] bg-[var(--admin-surface-muted)] p-4" data-page-section>
    <div class="mb-4 flex items-center justify-between gap-3">
        <strong class="text-sm">Content section <span data-section-number>{{ is_numeric($index) ? $index + 1 : '' }}</span></strong>
        <div class="flex gap-2"><button type="button" class="btn btn-secondary btn-sm" data-section-up aria-label="Move section up">↑</button><button type="button" class="btn btn-secondary btn-sm" data-section-down aria-label="Move section down">↓</button><button type="button" class="btn btn-danger btn-sm" data-section-remove>Remove</button></div>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div><label class="admin-label">Eyebrow</label><input name="sections[{{ $index }}][eyebrow]" class="admin-input" value="{{ $section['eyebrow'] ?? '' }}"></div>
        <div><label class="admin-label">Heading</label><input name="sections[{{ $index }}][heading]" class="admin-input" value="{{ $section['heading'] ?? '' }}"></div>
        <div class="md:col-span-2"><label class="admin-label">Body</label><textarea name="sections[{{ $index }}][body]" rows="4" class="admin-input">{{ $section['body'] ?? '' }}</textarea></div>
        <div class="md:col-span-2"><label class="admin-label">Image URL</label><input name="sections[{{ $index }}][image_url]" class="admin-input" value="{{ $section['image_url'] ?? '' }}"></div>
        <div><label class="admin-label">Button label</label><input name="sections[{{ $index }}][cta_label]" class="admin-input" value="{{ $section['cta_label'] ?? '' }}"></div>
        <div><label class="admin-label">Button URL</label><input name="sections[{{ $index }}][cta_url]" class="admin-input" value="{{ $section['cta_url'] ?? '' }}"></div>
        <div class="md:col-span-2"><input type="hidden" name="sections[{{ $index }}][is_enabled]" value="0"><label class="admin-check"><input type="checkbox" name="sections[{{ $index }}][is_enabled]" value="1" @checked((bool) ($section['is_enabled'] ?? true))> Show this section</label></div>
    </div>
</article>
