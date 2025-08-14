<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reason;
use Illuminate\Http\Request;

class ReasonController extends Controller
{
        public function index(Request $request)
    {
        $reasons = Reason::when($request->search, function ($q, $search) {
            $q->where('name', 'like', "%$search%");
        })->paginate(10);

        if ($request->ajax()) {
            return view('admin.reasons._list', compact('reasons'))->render();
        }

        return view('admin.reasons.index', compact('reasons'));
    }
    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('admin.reasons._form', [
                'action' => route('admin.reasons.store'),
                'method' => 'POST',
                'reason' => new Reason,
            ])->render();
        }
        return view('admin.reasons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:reasons,name',
        ]);

        Reason::create($request->only('name'));

        if ($request->ajax()) {
            $reasons = Reason::paginate(10);
            return view('admin.reasons._list', compact('reasons'))->render();
        }

        return redirect()->route('admin.reasons.index')->with('success', 'Reason added.');
    }

    public function edit(Request $request, Reason $reason)
    {
        if ($request->ajax()) {
            return view('admin.reasons._form', [
                'action' => route('admin.reasons.update', $reason),
                'method' => 'PUT',
                'reason' => $reason,
            ])->render();
        }
        return view('admin.reasons.edit', compact('reason'));
    }

    public function update(Request $request, Reason $reason)
    {
        $request->validate([
            'name' => 'required|string|unique:reasons,name,' . $reason->id,
        ]);

        $reason->update($request->only('name'));

        if ($request->ajax()) {
            $reasons = Reason::paginate(10);
            return view('admin.reasons._list', compact('reasons'))->render();
        }

        return redirect()->route('admin.reasons.index')->with('success', 'Reason updated.');
    }

    public function destroy(Request $request, Reason $reason)
    {
        $reason->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Reason deleted.']);
        }

        return redirect()->route('admin.reasons.index')->with('success', 'Reason deleted.');
    }
}
