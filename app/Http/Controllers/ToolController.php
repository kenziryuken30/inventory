<?php

namespace App\Http\Controllers;

use App\Models\InvSerialNumber;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    /**
     * DATA TOOLS
     */
    public function index()
    {
        $tools = InvSerialNumber::with([
            'toolkit.category'
        ])->orderBy('toolkit_id')->get();

        return view('tools.index', compact('tools'));
    }

    /**
     * SELESAI MAINTENANCE
     */
    public function finishMaintenance($id)
{
    $tool = InvSerialNumber::findOrFail($id);

    $tool->update([
        'condition' => 'baik'
    ]);

    return redirect()->back()->with('success', 'Maintenance selesai');
}

}
