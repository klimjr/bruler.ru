<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ProductCertificateSelector extends Component
{
    public Product $product;
    public $selectedCertificate;
    public $certificate_params = null;
    public $availableCertificates = [];
    public $firstInit = true;

    public function mount() {
        $this->prepareData();
    }

    public function render()
    {
        return view('livewire.product-certificate-selector');
    }

    public function selectCertificate(string $certificate_index = "0") {
        $this->firstInit = false;
        $this->selectedCertificate = $this->certificate_params[$certificate_index];
        $this->dispatch('productCertificateUpdated', cert: $this->selectedCertificate);
        $this->dispatch('productCertificateAdded', cert: $this->selectedCertificate);
    }

    public function prepareData() {
        if (count($this->product->certificate_params) >= 1) {
            $this->certificate_params = array_map(function($cert) {
                $cert['image'] = asset('storage/' . $cert['image']);
                return $cert;
            }, $this->product->certificate_params);
        }
      $this->availableCertificates = $this->certificate_params;
    }
}


