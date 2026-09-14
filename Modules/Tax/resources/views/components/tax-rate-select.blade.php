@props([
    'name' => 'tax_rate_id',
    'selected' => null,
    'taxRates' => [],
    'required' => false,
    'id' => null,
    'placeholder' => null,
])

<select
    id="{{ $id ?? $name }}"
    name="{{ $name }}"
    {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' => 'form-select form-select-solid']) }}
>
    <option value="">{{ $placeholder ?? __('tax::tax_rate.fields.none') }}</option>
    @foreach($taxRates as $rate)
        <option value="{{ $rate->id }}"
                data-percentage="{{ $rate->percentage }}"
                data-type="{{ $rate->type }}"
                @selected((int) old($name, $selected) === $rate->id)>
            {{ $rate->name }} ({{ number_format((float) $rate->percentage, 2) }}% — {{ __('tax::tax_rate.types.'.$rate->type) }})
        </option>
    @endforeach
</select>
