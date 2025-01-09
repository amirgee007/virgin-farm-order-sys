<table style="padding: 0 30px;">
    <thead>
    <tr>
        @foreach ($columns as $column)
            <th style="text-align: {{ $column === 'product_text' ? 'left' : 'center' }};">{{ @$columnCustomNames[$column] }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @php
        // Define colors for specific words
        $colors = [
            'YELLOW' => '#FFFF00',
            'WHITE' => '#FFFFFF',
            'RED' => '#FF7373',
            'PINK' => '#FFC0CB',
            'PEACH' => '#FFDAB9',
            'ORANGE' => '#FFA500',
            'CHAMPAGNE' => '#F7E7CE',
            'LAVENDER' => '#E6E6FA',
            'NOVELTY' => '#FFD700', //it seems GOLDEN
        ];
    @endphp
    @foreach ($groupedData as $categoryName => $products)
        @php
            // Split the category name into words and get the second word
             $words = explode(' ', $categoryName); // Convert to uppercase for case-insensitive match
             $secondWord = $words[1] ?? ''; // Get the second word or an empty string if not present

             // Check if the second word exists in the colors array otherwise default color
             $backgroundColor = $colors[$secondWord] ?? "#2a9d76";
        @endphp

        <!-- Display the category name with a random background color -->
        <tr>
            <td colspan="{{ count($columns) }}"
                style="font-weight: bold; text-align: left; background-color: {{$backgroundColor}};">
                {{ $categoryName }}
            </td>
        </tr>

        <!-- Loop through the products under this category -->
        @foreach ($products as $row)
            <tr>
                @foreach ($columns as $column)
                    <td style="text-align: {{ $column === 'product_text' ? 'left' : 'center' }};">
                        @if (str_contains($column, 'price'))
                            ${{ round2Digit($row[$column]) }}
                        @elseif($column == 'product_text')
                            {!!  $row->is_special>5 ? '⚡' : '' !!} {{ $row[$column] }}
                        @else
                            {{ $row[$column] }}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    @endforeach

    </tbody>
</table>
