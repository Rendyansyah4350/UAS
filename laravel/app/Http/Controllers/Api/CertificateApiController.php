<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Laravel\Sanctum\PersonalAccessToken;


class CertificateApiController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil sertifikat milik user yang sedang login beserta data kursusnya
        $certificates = Certificate::with('course')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $certificates
        ]);
    }

    public function downloadMobile(Request $request, $id)
    {

        $certificate = Certificate::with(['user', 'course'])->findOrFail($id);

        $tokenString = $request->query('token'); // Menangkap ?token=... dari Ionic

        if (!$tokenString)
        {
            return response()->json(['message' => 'Akses ditolak, token tidak ditemukan!'], 401);
        }

        $token = PersonalAccessToken::findToken($tokenString);

        if (!$token || !$token->tokenable)
        {
            return response()->json(['message' => 'Sesi token tidak valid atau telah kedaluwarsa!'], 401);
        }

        $userYangLogin = $token->tokenable;

        if ((int)$certificate->user_id != (int)$userYangLogin->id)
        {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akses ilegal! Ini bukan sertifikat Anda.'
            ], 403);
        }

        $pdf = Pdf::loadView('admin.certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'landscape')
            ->setWarnings(false);

        $pdfOutput = $pdf->output();


        return response($pdfOutput, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Sertifikat-' . str_replace(' ', '_', $certificate->user->name) . '.pdf"',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }
}
