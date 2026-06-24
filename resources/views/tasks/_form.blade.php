@php
    $selectedTags = collect(old('tags', isset($task) ? $task->tags->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
@endphp

<div class="mb-3">
    <label for="title" class="form-label">Title</label>
    <input
        type="text"
        class="form-control @error('title') is-invalid @enderror"
        id="title"
        name="title"
        value="{{ old('title', $task->title ?? '') }}"
        required
    >
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea
        class="form-control @error('description') is-invalid @enderror"
        id="description"
        name="description"
        rows="3"
    >{{ old('description', $task->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            <option value="pending" {{ old('status', $task->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="done" {{ old('status', $task->status ?? '') === 'done' ? 'selected' : '' }}>Done</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="deadline" class="form-label">Deadline</label>
        <input
            type="date"
            class="form-control @error('deadline') is-invalid @enderror"
            id="deadline"
            name="deadline"
            value="{{ old('deadline', isset($task) ? $task->deadline->format('Y-m-d') : '') }}"
            required
        >
        @error('deadline')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Tags</label>
    <div class="row g-2">
        @foreach ($tags as $tag)
            <div class="col-sm-6 col-lg-4">
                <div class="form-check border rounded p-2 ps-5 bg-light">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="tags[]"
                        value="{{ $tag->id }}"
                        id="tag-{{ $tag->id }}"
                        {{ in_array($tag->id, $selectedTags, true) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="tag-{{ $tag->id }}">{{ $tag->name }}</label>
                </div>
            </div>
        @endforeach
    </div>
    @error('tags')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
    @error('tags.*')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label for="attachment" class="form-label">Attachment</label>
    <input
        type="file"
        class="form-control @error('attachment') is-invalid @enderror"
        id="attachment"
        name="attachment"
        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
    >
    <div class="form-text">Accepted: PDF, JPG, PNG, DOC, DOCX. Maximum size: 5MB.</div>
    @error('attachment')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @if (isset($task) && $task->attachment_path)
        <div class="mt-2">
            <a href="{{ asset('storage/'.$task->attachment_path) }}" target="_blank">View current attachment</a>
        </div>
    @endif
</div>
