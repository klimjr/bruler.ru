<button {{ $attributes->merge(['class' => 'button-white button-text']) }} {{ $attributes }}>
  {{ $slot }}
</button>
