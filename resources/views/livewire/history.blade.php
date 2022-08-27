<div>

    <table style="margin-bottom: 20px">
        @foreach ($histories as $history)
            <tr>
                <td>
                    {{ $history->calculate_history }}
                </td>
                <td>=</td>
                <td>
                    {{ $history->result }}
                </td>
                <td>
                    <button wire:click="useHistory({{ $history->id }})">pakai</button>
                </td>
            </tr>
        @endforeach
    </table>
</div>
