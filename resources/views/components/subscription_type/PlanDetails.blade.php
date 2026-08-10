<?php
   $btnStyle = $plan->name == 'STANDARD' ? 'btn-primary text-white' : 'btn-outline-secondary'
?>
<div class="card mb-4">
   <div class="p-4">
      <span class="rounded-pill {{$plan->name}}">{{$plan->name}}</span>
   </div> 

   <div class="border-top"></div>

   <div class="p-4 styled-pricing-list">
      <h6 class="fw-bolder pb-2">{{__('Include')}}</h6>
      <ul class="list-unstyled">
         <ul class="list-unstyled">
            <li>
                <i class="fa-solid fa-badge-check"></i>
                {{$plan->biolinks}} Biolinks Create
            </li>
            <li>
                <i class="fa-solid fa-badge-check"></i>
                {{$plan->biolink_blocks}} Biolink Blocks Access
            </li>
            <li>
                <i class="fa-solid fa-badge-check"></i>
                {{$plan->shortlinks}} Shortlinks Create
            </li>
            
            <li>
                <i class="fa-solid fa-badge-check"></i>
                {{$plan->qrcodes}} QR Kod Oluşturma
            </li>
            <li>
                <i class="fa-solid fa-badge-check"></i>
                {{$plan->themes}} Temalara Erişim
            </li>
            <li>
                <i class="fa-solid fa-badge-check"></i>
                {{$plan->custom_theme ? 'Özel Tema Oluşturulabilir' : 'Özel Tema Oluşturulamaz'}}
            </li>
            <li>
                <i class="fa-solid fa-badge-check"></i>
                {{$plan->support}} Hours Support
            </li>
        </ul>
      </ul>
   </div>
</div>