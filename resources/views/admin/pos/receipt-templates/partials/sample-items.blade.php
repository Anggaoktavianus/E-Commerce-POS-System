<table>
    @foreach($items as $item)
    <tr>
        <td colspan="3">
            <strong>{{ $item->product_name }}</strong><br>
            <small>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</small>
        </td>
    </tr>
    <tr>
        <td></td>
        <td class="text-right">{{ $item->quantity }}</td>
        <td class="text-right">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
    </tr>
    @endforeach
</table>
