<div>
  <div class="text !text-[20px] !text-primary mt-4 md:mt-7">
    Номинал:
  </div>
  <div class="mt-2 border-black border-[2px] rounded-[9px] w-fit flex button-text-letter !text-[24px] !text-primary">
    @foreach($availableCertificates as $index => $cert)
      <div wire:key="certificate-{{ $cert['price'] }}" class="cursor-pointer @if($cert == $selectedCertificate && !$firstInit) bg-primary @endif transition-all duration-500 w-fit px-4 py-2 h-fit flex justify-center items-center @if(!$loop->last) border-r-[2px] @endif border-black">
        <div class="text-center cursor-pointer" @click="window.dispatchEvent(new CustomEvent('initSidebarScroll'))" wire:click="selectCertificate('{{ $index }}')">
          <div class="main-text !text-[12px] md:!text-[15px] @if($cert == $selectedCertificate && !$firstInit) !text-white @endif">{{ $cert['price'] }} ₽</div>
        </div>
      </div>
    @endforeach
  </div>
</div>
