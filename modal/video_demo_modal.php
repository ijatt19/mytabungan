<!-- Video Demo Modal -->
<div id="videoDemoModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4 transition-opacity duration-300 opacity-0" aria-labelledby="videoDemoModalLabel" aria-modal="true" role="dialog">
    <div class="relative w-full max-w-4xl bg-black rounded-2xl overflow-hidden shadow-2xl transform transition-all scale-95 duration-300" id="videoModalContent">
        <!-- Close Button -->
        <button type="button" class="absolute top-4 right-4 z-10 text-white/70 hover:text-white bg-black/50 hover:bg-black/70 rounded-full p-2 transition-all" onclick="closeDemoModal()">
            <i class="bi bi-x-lg text-xl"></i>
        </button>
        
        <!-- Video Container (16:9 Aspect Ratio) -->
        <div class="relative pt-[56.25%] w-full bg-slate-900">
            <iframe 
                id="demoVideoIframe"
                class="absolute top-0 left-0 w-full h-full"
                src="" 
                title="MyTabungan Demo Video" 
                frameborder="0" 
                allow="fullscreen">
            </iframe>
        </div>
    </div>
</div>

<script>
    const videoModal = document.getElementById('videoDemoModal');
    const videoModalContent = document.getElementById('videoModalContent');
    const videoIframe = document.getElementById('demoVideoIframe');
    // Placeholder Video ID (Nature video as placeholder) - User should replace this
    const videoId = 'dQw4w9WgXcQ'; // Rick Roll as placeholder? Or maybe something safer like a nature video. Let's use a generic tech background or similar if possible, but for now standard placeholder.
    // Actually, let's use a more neutral placeholder if possible, or just leave it empty/commented.
    // Let's use a generic placeholder video ID.
    const videoUrl = 'https://www.youtube.com/embed/LXb3EKWsInQ?autoplay=1&rel=0'; // Nature video 4K

    function openDemoModal() {
        videoModal.classList.remove('hidden');
        // Small delay to allow display:block to apply before opacity transition
        setTimeout(() => {
            videoModal.classList.remove('opacity-0');
            videoModalContent.classList.remove('scale-95');
            videoModalContent.classList.add('scale-100');
            videoIframe.src = videoUrl;
        }, 10);
    }

    function closeDemoModal() {
        videoModal.classList.add('opacity-0');
        videoModalContent.classList.remove('scale-100');
        videoModalContent.classList.add('scale-95');
        
        // Wait for transition to finish before hiding and stopping video
        setTimeout(() => {
            videoModal.classList.add('hidden');
            videoIframe.src = ''; // Stop video
        }, 300);
    }

    // Close on click outside
    videoModal.addEventListener('click', function(e) {
        if (e.target === videoModal) {
            closeDemoModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !videoModal.classList.contains('hidden')) {
            closeDemoModal();
        }
    });
</script>
