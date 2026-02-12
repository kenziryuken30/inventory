@extends('layouts.app')

@section('content')
<form method="POST" action="/consumable-return" class="p-4 space-y-4">
@csrf

<h1 class="text-xl font-bold">Return Consumable</h1>

<input name="employee_id" placeholder="ID Pegawai"
    class="border p-2 w-full">

<input type="date" name="date"
    class="border p-2 w-full">

<textarea name="note"
    placeholder="Catatan"
    class="border p-2 w-full"></textarea>

<table class="w-full border">
<thead>
<tr>
    <th>Consumable</th>
    <th>Qty Return</th>
</tr>
</thead>
<tbody>
@foreach($consumables as $c)
<tr>
    <td class="border p-2">
        {{ $c->name }} (stok: {{ $c->stock }})
        <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $c->id }}">
    </td>
    <td class="border p-2">
        <input type="number"
            name="items[{{ $loop->index }}][qty]"
            class="border p-1 w-20">
    </td>
</tr>
@endforeach
</tbody>
</table>

<button class="px-4 py-2 bg-blue-600 text-white rounded">
    Simpan Return
</button>

</form>
@endsection
