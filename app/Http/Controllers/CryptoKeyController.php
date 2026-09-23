<?php

namespace App\Http\Controllers;

use App\Models\CryptoKey;
use App\Services\CryptoService;
use Illuminate\Http\Request;

class CryptoKeyController
{
    protected CryptoService $cryptoService;

    public function __construct(CryptoService $cryptoService)
    {
        $this->cryptoService = $cryptoService;
    }

    /**
     * Display listing of cryptographic keys.
     */
    public function index()
    {
        $activeKey = $this->cryptoService->getActiveKey();
        $keys = CryptoKey::withCount('certificates')->latest()->get();

        return view('crypto.index', [
            'activeKey' => $activeKey,
            'keys' => $keys,
        ]);
    }

    /**
     * Trigger cryptographic key rotation.
     */
    public function rotate(Request $request)
    {
        $name = $request->input('key_name', 'Universitas Bina Sarana Informatika Rotated Key - '.now()->format('Y-m-d'));
        $newKey = $this->cryptoService->rotateKey($name);

        return redirect()->route('crypto-keys.index')
            ->with('status', "Kunci kriptografi RSA-2048 berhasil dirotasi. Kunci aktif baru: {$newKey->key_id}.");
    }

    /**
     * Download public key PEM file.
     */
    public function downloadPublic(CryptoKey $cryptoKey)
    {
        return response($cryptoKey->public_key, 200, [
            'Content-Type' => 'application/x-pem-file',
            'Content-Disposition' => "attachment; filename=\"certiva-public-key-{$cryptoKey->key_id}.pem\"",
        ]);
    }
}
