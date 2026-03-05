@php
    // Redirect to water meters by default, or electric, or a more general page
    // For now, redirect to water meters, as it's a common starting point.
    $selectedMonth = request('month', \Carbon\Carbon::now()->format('Y-m'));
@endphp

<script>
    window.location.href = "{{ route('meters.water') }}?month={{ $selectedMonth }}";
</script>