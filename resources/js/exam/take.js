export function initExamProctor(attemptId, csrfToken) {

    let violationCount = 0;
    let violationLimit = 0;
    let isSubmitting = false;

    let webcamEnabled = false;
    let webcamChecked = false;
    let allowCameraGrace = true;

    const examContent = document.getElementById('examContent');
    const gate = document.getElementById('fullscreenGate');
    const btn = document.getElementById('enterFullscreenBtn');
    const form = document.getElementById('examForm');


    btn?.addEventListener('click', async () => {
        try {
            await document.documentElement.requestFullscreen();

            gate.style.display = 'none';
            examContent.style.display = 'block';

        } catch (err) {
            alert("Fullscreen is required to start the exam.");
        }
    });

  
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement && !isSubmitting) {

            if (allowCameraGrace) return;

            logViolation('exit_fullscreen');

            examContent.style.display = 'none';
            gate.style.display = 'block';
        }
    });


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


    function showWarning(type) {
        const box = document.getElementById('warningBox');
        if (!box) return;

        box.innerText = `Warning: ${type} (${violationCount}/${violationLimit})`;
        box.style.display = 'block';
    }


    document.addEventListener("visibilitychange", () => {
        if (document.hidden) {
            if (allowCameraGrace) return;
            logViolation('tab_switch');
        }
    });

    window.addEventListener("blur", () => {
        if (allowCameraGrace) return;
        logViolation('window_blur');
    });

    document.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        if (allowCameraGrace) return;
        logViolation('right_click');
    });

    document.addEventListener('copy', () => {
        if (allowCameraGrace) return;
        logViolation('copy');
    });

    document.addEventListener('paste', () => {
        if (allowCameraGrace) return;
        logViolation('paste');
    });


    const video = document.createElement('video');
    const canvas = document.createElement('canvas');

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            video.srcObject = stream;
            video.play();

            webcamEnabled = true;
            webcamChecked = true;
            allowCameraGrace = false;

   
            const track = stream.getVideoTracks()[0];
            if (track) {
                track.onended = () => {
                    if (isSubmitting) return;
                    logViolation('webcam_turned_off');
                };
            }
        })
        .catch(err => {
            webcamChecked = true;

            if (!allowCameraGrace) {
                logViolation('webcam_denied');
            }
        });


    setTimeout(() => {
        allowCameraGrace = false;

        if (!webcamEnabled && webcamChecked) {
            logViolation('webcam_denied');
        }
    }, 10000);


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

        if (!isSubmitting && document.fullscreenElement && webcamEnabled) {
            captureScreenshot();
        }

        const next = Math.random() * (60000 - 20000) + 20000;

        setTimeout(randomShot, next);
    }

    setTimeout(randomShot, 10000);
}