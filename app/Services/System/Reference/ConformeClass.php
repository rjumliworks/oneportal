<?php

namespace App\Services\System\Reference;

use App\Models\SupplierConforme;
use App\Http\Resources\FAIMS\Procurement\ConformeResource;
use Illuminate\Support\Facades\Auth;

class ConformeClass
{
     public function lists(){
        $data = SupplierConforme::with('created_by')->get();
        return $data;
    }

    public function save($request)
    {
        $conforme = SupplierConforme::create([
            'name' => $request->name,
            'contact_no' => $request->contact_no,
            'position' => $request->position,
            'is_active' => $request->is_active ?? 1,
            'user_id' => Auth::id(),
        ]);

        return [
            'data' => new ConformeResource($conforme->load(['created_by'])),
            'message' => 'Conforme created successfully!',
            'info' => "You've successfully added new Conforme.",
        ];
    }

    public function update($request)
    {
        $conforme = SupplierConforme::findOrFail($request->id);

        $conforme->update([
            'name' => $request->name,
            'contact_no' => $request->contact_no,
            'position' => $request->position,
            'is_active' => $request->is_active ?? $conforme->is_active,
        ]);

        return [
            'data' => new ConformeResource($conforme->load(['created_by'])),
            'message' => 'Conforme updated successfully!',
            'info' => "You've successfully updated the Conforme.",
        ];
    }

    public function status($request, $id)
    {
        $conforme = SupplierConforme::findOrFail($id);
        $conforme->update(['is_active' => $request->status]);

        return [
            'data' => new ConformeResource($conforme->load(['created_by'])),
            'message' => 'Conforme status updated successfully!',
            'info' => "You've successfully updated the Conforme status.",
        ];
    }

    public function delete($id)
    {
        $conforme = SupplierConforme::findOrFail($id);
        $conforme->delete();

        return [
            'message' => 'Conforme deleted successfully!',
            'info' => "You've successfully deleted the Conforme.",
        ];
    }
}
