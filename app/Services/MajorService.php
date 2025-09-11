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
        $major = Major::create($data);

        if ($request && $request->icon) {
            if ($major->icon) {
                Storage::disk('local')->delete($major->icon);
            }

            $file = $request->icon;
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = 'majors';

            Storage::disk('public')->makeDirectory($path);
            $relativePath = $path . '/' . $filename;
            Storage::disk('public')->put($relativePath, file_get_contents($file));

            $major->icon = $relativePath;
            $major->save();
        }
    }

    public function updateMajor($major, $validated, Request $request)
    {
        $updateData = [];

        if ($request && $request->icon) {
            if ($major->icon) {
            Storage::disk('public')->delete($major->icon);
            }

            $file = $request->icon;
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = 'majors';

            Storage::disk('public')->makeDirectory($path);
            $relativePath = $path . '/' . $filename;
            Storage::disk('public')->put($relativePath, file_get_contents($file));

            $updateData['icon'] = $relativePath;
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
