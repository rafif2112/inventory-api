<?php

namespace App\Services;

use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MajorService
{

    public function getAllMajors()
    {
        return Major::select('*')
            ->latest()
            ->get();
    }

    public function storeMajor(Request $request, array $data)
    {

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

        Major::create($data);
    }

    public function updateMajor($major, $validated, Request $request)
    {
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
        return $major;
    }


    public function deleteMajor($major)
    {
        if ($major->icon && Storage::disk('local')->exists($major->icon)) {
            Storage::disk('local')->delete($major->icon);
        }

        $major->whereId($major->id)->delete();
    }
}
