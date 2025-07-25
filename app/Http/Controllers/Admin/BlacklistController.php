<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blacklist;
use Illuminate\Http\Request;

class BlacklistController extends Controller
{
    public function index()
    {
        $blacklists = Blacklist::latest()->paginate(10);
        return view('admin.blacklist.index', compact('blacklists'));
    }

    public function create()
    {
        return view('admin.blacklist.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nic' => 'nullable|string',
            'full_name' => 'nullable|string',
            'company_name' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            'reason' => 'required|string',
        ]);

        Blacklist::create($data);
        return redirect()->route('blacklist.index')->with('success', 'Blacklist entry added.');
    }

    public function edit(Blacklist $blacklist)
    {
        return view('admin.blacklist.edit', compact('blacklist'));
    }

    public function update(Request $request, Blacklist $blacklist)
    {
        $data = $request->validate([
            'nic' => 'nullable|string',
            'full_name' => 'nullable|string',
            'company_name' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            'reason' => 'required|string',
        ]);

        $blacklist->update($data);
        return redirect()->route('blacklist.index')->with('success', 'Blacklist entry updated.');
    }

    public function destroy(Blacklist $blacklist)
    {
        $blacklist->delete();
        return redirect()->route('blacklist.index')->with('success', 'Blacklist entry deleted.');
    }
}
