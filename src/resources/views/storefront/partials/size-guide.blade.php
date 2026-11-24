@php
    $rows = [
        6 => [34, 26, 36], 8 => [36, 28, 38], 10 => [38, 30, 40], 12 => [40, 32, 42],
        14 => [42, 34, 44], 16 => [44, 36, 46], 18 => [46, 38, 48], 20 => [48, 40, 50],
        22 => [50, 42, 52], 24 => [52, 44, 54],
    ];
@endphp

<div class="my-8 overflow-hidden rounded-xl border border-wine-200 shadow-sm">
    <div class="bg-wine-700 px-5 py-4">
        <p class="font-display text-xl text-cream">UK Size Chart</p>
        <p class="text-xs text-wine-200 mt-0.5">All measurements in inches</p>
    </div>
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-wine-50 text-wine-900 text-xs uppercase tracking-wider">
                <th class="px-4 py-3 text-left font-semibold">UK Size</th>
                <th class="px-4 py-3 text-center font-semibold">Bust</th>
                <th class="px-4 py-3 text-center font-semibold">Waist</th>
                <th class="px-4 py-3 text-center font-semibold">Hip</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $size => [$bust, $waist, $hip])
                <tr class="{{ $loop->even ? 'bg-wine-50/50' : 'bg-white' }}">
                    <td class="px-4 py-2.5 font-semibold">{{ $size }}</td>
                    <td class="px-4 py-2.5 text-center">{{ $bust }}</td>
                    <td class="px-4 py-2.5 text-center">{{ $waist }}</td>
                    <td class="px-4 py-2.5 text-center">{{ $hip }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
