export function initExamProctor(attemptId, csrfToken) {

    let violationCount = 0;
    let violationLimit = 0;
    let isSubmitting = false;

    const examContent = document.getElementById('examContent');
    const gate = document.getElementById('fullscreenGate');
    const btn = document.getElementById('enterFullscreenBtn');
    const form = document.getElementById('examForm');

    // ==============================
    // 🔓 ENTER FULLSCREEN
    // ==============================
    btn?.addEventListener('click', async () => {
        try {
            await document.documentElement.requestFullscreen();

            gate.style.display = 'none';
            examContent.style.display = 'block';

        } catch (err) {
            alert("Fullscreen is required to start the exam.");
        }
    });

    // ==============================
    // 🚨 EXIT FULLSCREEN
    // ==============================
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement && !isSubmitting) {
            logViolation('exit_fullscreen');

            examContent.style.display = 'none';
            gate.style.display = 'block';
        }
    });

    // ==============================
    // 🔧 LOG VIOLATION
    // ==============================
    async function logViolation(type, data = null) {

        if (isSubmitting) return;

        try {
            const res = await fetch(`/attempt/${attemptId}/violation`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ type, data })
            });

            const result = await res.json();

            violationCount = result.count;
            violationLimit = result.limit;

            if (type !== 'screenshot') {
                showWarning(type);
            }

            if (result.exceeded) {
                isSubmitting = true;

                alert("Violation limit reached. Exam will be submitted.");

                form.submit();
            }

        } catch (err) {
            console.error("Violation error:", err);
        }
    }

    // ==============================
    // ⚠️ WARNING UI
    // ==============================
    function showWarning(type) {
        const box = document.getElementById('warningBox');
        if (!box) return;

        box.innerText = `Warning: ${type} (${violationCount}/${violationLimit})`;
        box.style.display = 'block';
    }

    // ==============================
    // 🚨 DETECTIONS
    // ==============================
    document.addEventListener("visibilitychange", () => {
        if (document.hidden) logViolation('tab_switch');
    });

    window.addEventListener("blur", () => {
        logViolation('window_blur');
    });

    document.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        logViolation('right_click');
    });

    document.addEventListener('copy', () => logViolation('copy'));
    document.addEventListener('paste', () => logViolation('paste'));

    // ==============================
    // 📸 WEBCAM SETUP
    // ==============================
    const video = document.createElement('video');
    const canvas = document.createElement('canvas');
    let webcamEnabled = false;

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            video.srcObject = stream;
            video.play();
            webcamEnabled = true;
        })
        .catch(() => logViolation('webcam_denied'));

    function captureScreenshot() {
        if (!webcamEnabled || !video.videoWidth || isSubmitting) return;

        const ctx = canvas.getContext('2d');

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        ctx.drawImage(video, 0, 0);

        const image = canvas.toDataURL('image/png');
        logViolation('screenshot', image);
    }

    function randomShot() {

        if (!isSubmitting && document.fullscreenElement) {
            captureScreenshot();
        }

        const next = Math.random() * (60000 - 20000) + 20000;

        setTimeout(randomShot, next);
    }

    setTimeout(randomShot, 10000);
}