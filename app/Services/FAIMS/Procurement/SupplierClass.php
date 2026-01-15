<?php

namespace App\Services\FAIMS\Procurement;

use App\Models\Supplier;
use App\Http\Resources\FAIMS\Procurement\SupplierResource;
use Illuminate\Support\Facades\Auth;

class SupplierClass
{
    public function lists($request){
        $data = SupplierResource::collection(
            Supplier::with(['address', 'conformes', 'attachments'])
            ->when($request->keyword, function ($query, $keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%")
                        ->orWhere('code', 'LIKE', "%{$keyword}%")
                        ->orWhereHas('address', function ($q) use ($keyword) {
                            $q->where('address', 'LIKE', "%{$keyword}%");
                        });
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->orderBy('created_at','DESC')
            ->paginate($request->count ?: 15)
        );
        return $data;
    }

    public function save($request)
    {
        $code = Supplier::generateCode();
        // Create the PAP Code with the correct syntax
        $supplier = Supplier::create($request->only('name', ) + ['code' => $code]);

        return [
            'data' => new SupplierResource($procurement_code),
            'message' => 'Supplier created successfully!',
            'info' => "You've successfully added new Supplier.",
        ];
    }

    public function update($request)
    {
        // Find the existing Supplier by ID
        $supplier = Supplier::findOrFail($request->id);

        // Update the Supplier with the new data
        $supplier->update($request->only(
                'name', 
                'code', 
            )
        );

        return [
            'data' => new SupplierResource($supplier),
            'message' => 'Supplier updated successfully!',
            'info' => "You've successfully updated the Supplier.",
        ];
    }

}
