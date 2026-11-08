<?php

namespace App\Http\Controllers\Backend\MyProfile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ActivityRecorder;
use Carbon\Carbon;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;
use Validator;

class AccountController extends Controller
{
    public function __construct() {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {

        return view('backend.my_profile.index');
    }

    public function editAvatar($id, User $user)
    {

        $html = view('backend.my_profile.change_pic',
            [
                'user' => $user->findOrFail($id),
            ])->render();

        return response()->json(['html' => $html]);
    }

    public function updateAvatar(Request $request, $id)
    {
        $formattedTime = Carbon::now()->diffForHumans();

        // ========================
        // VALIDASI
        // ========================
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ], [
            'avatar.required' => 'Foto wajib di upload.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Foto harus berformat JPG atau PNG.',
            'avatar.max' => 'Ukuran file foto maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        try {

            DB::beginTransaction();

            $user = User::findOrFail($id);

            // IDOR: hanya pemilik akun (atau Superadmin) yang boleh mengganti foto.
            abort_unless((string) $user->id === (string) auth()->id() || auth()->user()?->hasRole('Superadmin'), 403);

            // ===============================
            // FULL OLD SNAPSHOT
            // ===============================
            $oldData = $user->toArray();

            // ===============================
            // UPDATE AVATAR
            // ===============================
            if ($request->hasFile('avatar')) {

                // Hapus file lama
                if ($user->avatar && Storage::disk('public')->exists('user/avatar/'.$user->avatar)) {
                    Storage::disk('public')->delete('user/avatar/'.$user->avatar);
                }

                $file = $request->file('avatar');
                // Ekstensi dari isi berkas, bukan dari nama kiriman klien.
                $extension = $file->guessExtension() === 'png' ? 'png' : 'jpg';

                // Nama file aman & standar
                $filename = 'avatar-'.$user->id.'-'.time().'.'.$extension;

                // Simpan file
                Storage::disk('public')->putFileAs(
                    'user/avatar/',
                    $file,
                    $filename
                );

                $user->avatar = $filename;
            }

            $user->save();

            // ===============================
            // FULL NEW SNAPSHOT
            // ===============================
            $newData = $user->toArray();

            // ===============================
            // LOG ACTIVITY (AUDIT FULL)
            // ===============================

            ActivityRecorder::updated('profile', 'Mengganti Avatar', $oldData, $newData, $user);

            DB::commit();

            return response()->json([
                'success' => 'Avatar berhasil diperbaharui.',
                'time' => $formattedTime,
                'judul' => 'Berhasil',
                'avatar_url' => $user->avatar_display_url,
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'error' => 'Terjadi kesalahan di aplikasi, hubungi Developer.',
                'time' => $formattedTime,
                'judul' => 'Aplikasi Error',
                'errorMessage' => $e->getMessage(),
            ], 500);
        }
    }
}
