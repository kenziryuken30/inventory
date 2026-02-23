<div class="modal fade" id="modalReturn{{ $transaction->id }}">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form method="POST" action="{{ route('transaksi.returnStore') }}">
@csrf

<input type="hidden" name="transaction_id" value="{{ $transaction->id }}">

<div class="modal-header">
    <h5 class="modal-title">Return Consumable</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<div class="row mb-3">
    <div class="col-md-6">
        <label>Karyawan</label>
        <input class="form-control"
               value="{{ $transaction->borrower_name }}"
               readonly>
    </div>

    <div class="col-md-6">
        <label>Tanggal</label>
        <input type="date"
               name="date"
               class="form-control"
               value="{{ date('Y-m-d') }}">
    </div>
</div>

<textarea name="note"
    class="form-control mb-3"
    placeholder="Catatan"></textarea>

<table class="table table-bordered">
<thead>
<tr>
    <th>Consumable</th>
    <th>Qty Return</th>
</tr>
</thead>
<tbody>

@foreach($transaction->items as $item)
<tr>
    <td>
        {{ $item->consumable->name }}
        ({{ $item->qty }} {{ $item->consumable->unit }})

        <input type="hidden"
               name="items[{{ $loop->index }}][id]"
               value="{{ $item->consumable_id }}">
    </td>

    <td>
        <input type="number"
               name="items[{{ $loop->index }}][qty]"
               class="form-control">
    </td>
</tr>
@endforeach

</tbody>
</table>

</div>

<div class="modal-footer">
    <button class="btn btn-primary">
        Kembalikan
    </button>
</div>

</form>

</div>
</div>
</div>
