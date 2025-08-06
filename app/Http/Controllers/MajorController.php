<?php

namespace App\Http\Controllers;

use App\Http\Requests\Major\StoreValidate;
use App\Http\Requests\Major\UpdateValidate;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MajorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = Major::select('*')
        ->latest()
        ->get();

        return response()->json([
            'status' => 200,
            'data' => $data,
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreValidate $request)
    {
        //
        $data = $request->validated();

        DB::beginTransaction();
        try {

            if ($request->icon) {
                $icon = $request->icon;
                if (preg_match('/^data:image\/(\w+);base64,/', $icon, $type)) {
                    $format = strtolower($type[1]); // jpg, png, jpeg, dll.

                    if (!in_array($format, ['jpg', 'jpeg', 'png', 'webp'])) {
                        return response()->json([
                            'status' => 400,
                            'message' => 'Invalid image format',
                        ], 400);
                    }
                    $icon = preg_replace('/^data:image\/\w+;base64,/', '', $icon);
                    $icon = str_replace(' ', '+', $icon);
                    $filename = 'majors/' . time() . '-' . Str::slug($request->name) . '.' . $format;
                    Storage::disk('local')->put($filename, base64_decode($icon));
                    $data['icon'] = $filename;
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Invalid base64 string',
                    ], 400);
                }
            }

            $major = Major::create($data);

            DB::commit();

            return response()->json([
                'status' => 201,
                'message' => 'Major created successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to create major',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Major $major)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateValidate $request, Major $major)
    {
        //

        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $updateData = [];

            if ($request->icon) {
                $icon = $request->icon;
                if (preg_match('/^data:image\/(\w+);base64,/', $icon, $type)) {
                    $format = strtolower($type[1]);
                    if (!in_array($format, ['jpg', 'jpeg', 'png', 'webp'])) {
                        return response()->json([
                            'status' => 400,
                            'message' => 'Invalid image format',
                        ], 400);
                    }

                    $icon = preg_replace('/^data:image\/\w+;base64,/', '', $icon);
                    $icon = str_replace(' ', '+', $icon);
                    $filename = 'majors/' . time() . '-' . Str::slug($request->name) . '.' . $format;
                    if ($major->icon) {
                        Storage::disk('local')->delete($major->icon);
                    }

                    Storage::disk('local')->put($filename, base64_decode($icon));
                    $updateData['icon'] = $filename;
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Invalid base64 string',
                    ], 400);
                }
            }

            $major->whereId($major->id)->update(array_merge($validated, $updateData));

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Major updated successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update major',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Major $major)
    {
        //
        DB::beginTransaction();
        try {
            if ($major->icon && Storage::disk('local')->exists($major->icon)) {
                Storage::disk('local')->delete($major->icon);
            }

            $major->whereId($major->id)->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Major deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete major',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
