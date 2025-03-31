<?php

namespace App\Livewire;

use App\Models\Certificate;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Component;

class CertificateCheck extends Component
{
    public $certificate;
    public $error = false;
    public $message = '';

    public function mount($certificate)
    {
        $this->certificate = $certificate ?? '';
    }

    public function render()
    {
        return view('livewire.certificate-check');
    }

    public function updatedCertificate()
    {
        $cert = Certificate::where('code', $this->certificate)
            // ->where('target_email', $this->email)
            ->where('expires_at', '>=', now())
            ->where('remains', '>=', 1)
            ->first();
        if ($cert) {
            $this->certError = false;
            $this->certMessage = 'Сертификат активирован';
            $this->useCertificate = (bool)$cert;
            $this->db_typedCertificate = ($cert) ?: null;
            $this->cert_amount = $cert->remains;
            $this->dispatch('certificateUsed', [
                'certificate' => $cert->code,
                'remains' => $cert->remains
            ]);
        } else {
            $this->certError = true;
            $this->certMessage = 'Неверный сертификат';

        }
        $this->error = $this->certError;
        $this->message = $this->certMessage;
    }

    public function resetGlobal()
    {
        $this->message = '';
        $this->certificate = '';
        $this->dispatch('certificateCancel');
    }
}
