@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'error' => true, 'hint' => null, 'rows' => 3])
@php $id = $name.'-'.\Illuminate\Support\Str::random(5); @endphp
<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $id }}" class="form-label">{{ $label }}@if ($attributes->has('required')) <span class="text-brand-600">*</span>@endif</label>
    @endif
    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" {{ $attributes->except('class')->class(['form-input', 'border-accent' => $error && $errors->has($name)]) }}>{{ $value }}</textarea>
    @elseif ($type === 'select')
        <select id="{{ $id }}" name="{{ $name }}" {{ $attributes->except('class')->class(['form-input', 'border-accent' => $error && $errors->has($name)]) }}>{{ $slot }}</select>
    @else
        <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" @if ($type !== 'password' && $type !== 'file') value="{{ $value }}" @endif {{ $attributes->except('class')->class(['form-input', 'border-accent' => $error && $errors->has($name)]) }}>
    @endif
    @if ($hint)<p class="mt-1 text-xs text-ink-500">{{ $hint }}</p>@endif
    @if ($error)@error($name)<p class="form-error">{{ $message }}</p>@enderror @endif
</div>
