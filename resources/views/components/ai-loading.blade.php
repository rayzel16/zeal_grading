<div wire:loading.flex 
     wire:target="generateBulk,regenerate"
     style="
        position: fixed;
        top:0; left:0;
        width:100%; height:100%;
        background: rgba(0,0,0,0.6);
        z-index:9999;
        align-items:center;
        justify-content:center;
        flex-direction:column;
        color:white;
     ">

    <img src="{{ asset('images/generate-questions.gif') }}"
         alt="Loading..."
         style="width:120px; height:auto;" class="mb-3">

    <div>🤖 Generating AI Questions...</div>

</div>