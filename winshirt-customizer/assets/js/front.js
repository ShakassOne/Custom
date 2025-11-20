(function () {
    const root = document.querySelector('.winshirt-customizer');
    if (!root) return;

    root.style.setProperty('--winshirt-theme', root.dataset.themeColor || '#3b82f6');
    const overlay = root.querySelector('[data-overlay]');
    const preview = root.querySelector('[data-preview]');
    const layerList = root.querySelector('[data-layer-list]');
    const canvas2d = document.getElementById('winshirt-2d-canvas');
    const ctx2d = canvas2d.getContext('2d');
    const canvas3d = document.getElementById('winshirt-3d-canvas');

    const state = {
        face: 'front',
        layers: [],
        history: [],
        future: [],
    };

    const overlayRect = () => overlay.getBoundingClientRect();

    function pushHistory() {
        state.history.push(JSON.parse(JSON.stringify(state.layers)));
        if (state.history.length > 50) state.history.shift();
        state.future = [];
    }

    function refreshLayerList() {
        layerList.innerHTML = '';
        state.layers
            .filter((layer) => layer.face === state.face)
            .forEach((layer) => {
                const item = document.createElement('div');
                item.className = 'winshirt-layer-item';
                item.dataset.id = layer.id;
                item.innerHTML = `<span>${layer.label}</span>`;
                const controls = document.createElement('div');
                controls.className = 'controls';
                const lock = document.createElement('button');
                lock.textContent = layer.locked ? '🔒' : '🔓';
                lock.addEventListener('click', () => {
                    layer.locked = !layer.locked;
                    pushHistory();
                    refresh();
                });
                const hide = document.createElement('button');
                hide.textContent = layer.hidden ? '👁‍🗨' : '👁';
                hide.addEventListener('click', () => {
                    layer.hidden = !layer.hidden;
                    pushHistory();
                    refresh();
                });
                const del = document.createElement('button');
                del.textContent = '🗑';
                del.addEventListener('click', () => {
                    state.layers = state.layers.filter((l) => l.id !== layer.id);
                    pushHistory();
                    refresh();
                });
                controls.append(lock, hide, del);
                item.appendChild(controls);
                layerList.appendChild(item);
            });
    }

    function render2D() {
        ctx2d.clearRect(0, 0, canvas2d.width, canvas2d.height);
        const zone = overlay.getBoundingClientRect();
        const container = canvas2d.getBoundingClientRect();
        const offsetX = (zone.left - container.left) * (canvas2d.width / container.width);
        const offsetY = (zone.top - container.top) * (canvas2d.height / container.height);
        const zoneW = zone.width * (canvas2d.width / container.width);
        const zoneH = zone.height * (canvas2d.height / container.height);
        ctx2d.fillStyle = '#0f172a';
        ctx2d.fillRect(0, 0, canvas2d.width, canvas2d.height);
        ctx2d.save();
        ctx2d.beginPath();
        ctx2d.rect(offsetX, offsetY, zoneW, zoneH);
        ctx2d.clip();
        state.layers
            .filter((layer) => layer.face === state.face && !layer.hidden)
            .forEach((layer) => {
                ctx2d.save();
                const x = offsetX + layer.x * zoneW;
                const y = offsetY + layer.y * zoneH;
                const w = layer.width * zoneW;
                const h = layer.height * zoneH;
                ctx2d.translate(x + w / 2, y + h / 2);
                ctx2d.rotate((layer.rotation * Math.PI) / 180);
                ctx2d.globalAlpha = layer.opacity;
                if (layer.type === 'text') {
                    ctx2d.fillStyle = layer.color;
                    ctx2d.font = `${Math.round(layer.fontSize * 4)}px ${layer.font}`;
                    ctx2d.textAlign = 'center';
                    ctx2d.fillText(layer.text, 0, 0);
                } else if (layer.image) {
                    ctx2d.drawImage(layer.image, -w / 2, -h / 2, w, h);
                }
                ctx2d.restore();
            });
        ctx2d.restore();
    }

    function refreshPreview() {
        const url = canvas2d.toDataURL('image/png');
        preview.style.backgroundImage = `url(${url})`;
        preview.style.backgroundSize = 'contain';
        preview.style.backgroundRepeat = 'no-repeat';
        preview.style.backgroundPosition = 'center';
    }

    function refreshOverlay() {
        root.querySelectorAll('.winshirt-layer').forEach((el) => el.remove());
        const zone = overlay.getBoundingClientRect();
        state.layers
            .filter((layer) => layer.face === state.face && !layer.hidden)
            .forEach((layer) => {
                const div = document.createElement('div');
                div.className = 'winshirt-layer';
                div.style.width = `${layer.width * 100}%`;
                div.style.height = `${layer.height * 100}%`;
                div.style.left = `${layer.x * 100}%`;
                div.style.top = `${layer.y * 100}%`;
                div.style.opacity = layer.opacity;
                div.style.transform = `rotate(${layer.rotation}deg)`;
                div.textContent = layer.label;
                div.dataset.id = layer.id;
                const resize = document.createElement('span');
                resize.className = 'winshirt-handle resize';
                const rotate = document.createElement('span');
                rotate.className = 'winshirt-handle rotate';
                div.append(resize, rotate);
                overlay.parentElement.appendChild(div);
                bindLayer(div, layer, zone);
            });
    }

    function refresh() {
        refreshLayerList();
        refreshOverlay();
        render2D();
        refreshPreview();
        update3DTexture();
        localStorage.setItem('winshirt_customizer_state', JSON.stringify(state));
    }

    function addLayer(layer) {
        pushHistory();
        state.layers.push(layer);
        refresh();
    }

    function bindTabs() {
        const buttons = root.querySelectorAll('.winshirt-tabs button');
        buttons.forEach((btn) => {
            btn.addEventListener('click', () => {
                buttons.forEach((b) => b.classList.remove('active'));
                btn.classList.add('active');
                root.querySelectorAll('.winshirt-tab-content').forEach((tab) => {
                    tab.hidden = tab.dataset.tab !== btn.dataset.tab;
                });
            });
        });
    }

    function bindFaces() {
        root.querySelectorAll('[data-face]').forEach((btn) => {
            btn.addEventListener('click', () => {
                state.face = btn.dataset.face;
                refresh();
            });
        });
    }

    function bindUploads() {
        root.querySelectorAll('.winshirt-upload input[type="file"]').forEach((input) => {
            input.addEventListener('change', (e) => {
                const file = e.target.files?.[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = () => {
                    const img = new Image();
                    img.onload = () => {
                        addLayer({
                            id: crypto.randomUUID(),
                            type: 'image',
                            image: img,
                            label: file.name,
                            x: 0.2,
                            y: 0.2,
                            width: 0.3,
                            height: 0.3,
                            rotation: 0,
                            opacity: 1,
                            face: state.face,
                        });
                    };
                    img.src = reader.result;
                };
                reader.readAsDataURL(file);
            });
        });
    }

    function bindTextControls() {
        const textInput = root.querySelector('[data-control="text-value"]');
        const addText = () => {
            if (!textInput.value) return;
            addLayer({
                id: crypto.randomUUID(),
                type: 'text',
                text: textInput.value,
                label: `Texte: ${textInput.value.substring(0, 12)}`,
                font: root.querySelector('[data-control="text-font"]').value,
                fontSize: parseInt(root.querySelector('[data-control="text-size"]').value, 10),
                spacing: parseInt(root.querySelector('[data-control="text-spacing"]').value, 10),
                align: root.querySelector('[data-control="text-align"]').value,
                color: root.querySelector('[data-control="text-color"]').value,
                stroke: root.querySelector('[data-control="text-stroke"]').value,
                curved: root.querySelector('[data-control="text-curve"]').checked,
                x: 0.25,
                y: 0.3,
                width: 0.5,
                height: 0.1,
                rotation: 0,
                opacity: 1,
                face: state.face,
            });
        };
        textInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                addText();
            }
        });
    }

    function bindQR() {
        const button = root.querySelector('[data-action="generate-qr"]');
        if (!button) return;
        button.addEventListener('click', () => {
            const url = root.querySelector('[data-control="qr-url"]').value;
            if (!url) return;
            fetch(`https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=${encodeURIComponent(url)}`)
                .then((res) => res.blob())
                .then((blob) => {
                    const img = new Image();
                    img.onload = () => addLayer({
                        id: crypto.randomUUID(),
                        type: 'image',
                        image: img,
                        label: 'QR',
                        x: 0.35,
                        y: 0.35,
                        width: 0.25,
                        height: 0.25,
                        rotation: 0,
                        opacity: 1,
                        face: state.face,
                    });
                    img.src = URL.createObjectURL(blob);
                });
        });
    }

    function bindAI() {
        const button = root.querySelector('[data-action="generate-ai"]');
        if (!button) return;
        button.addEventListener('click', () => {
            const prompt = root.querySelector('[data-control="ai-prompt"]').value;
            const style = root.querySelector('[data-control="ai-style"]').value;
            const text = new Blob([`IA: ${prompt} (${style})`]);
            const reader = new FileReader();
            reader.onload = () => {
                const img = new Image();
                img.onload = () => addLayer({
                    id: crypto.randomUUID(),
                    type: 'image',
                    image: img,
                    label: 'IA',
                    x: 0.3,
                    y: 0.3,
                    width: 0.4,
                    height: 0.3,
                    rotation: 0,
                    opacity: 1,
                    face: state.face,
                });
                img.src = reader.result;
            };
            reader.readAsDataURL(text);
        });
    }

    function bindLayer(div, layer, zone) {
        let startX = 0;
        let startY = 0;
        let startWidth = 0;
        let startHeight = 0;
        let startRotation = 0;
        let isResizing = false;
        let isRotating = false;

        const onPointerMove = (e) => {
            if (layer.locked) return;
            const rect = overlayRect();
            if (isResizing) {
                const deltaX = (e.clientX - startX) / rect.width;
                const size = Math.max(0.05, Math.min(0.9, Math.max(startWidth + deltaX, startHeight + deltaX)));
                layer.width = size;
                layer.height = size;
            } else if (isRotating) {
                const centerX = rect.left + rect.width * (layer.x + layer.width / 2);
                const centerY = rect.top + rect.height * (layer.y + layer.height / 2);
                const angle = Math.atan2(e.clientY - centerY, e.clientX - centerX);
                layer.rotation = (angle * 180) / Math.PI;
            } else {
                const deltaX = (e.clientX - startX) / rect.width;
                const deltaY = (e.clientY - startY) / rect.height;
                layer.x = Math.min(Math.max(0, startWidth + deltaX), 1 - layer.width);
                layer.y = Math.min(Math.max(0, startHeight + deltaY), 1 - layer.height);
            }
            refresh();
        };

        const endPointer = () => {
            document.removeEventListener('pointermove', onPointerMove);
            document.removeEventListener('pointerup', endPointer);
        };

        div.addEventListener('pointerdown', (e) => {
            if (layer.locked) return;
            const target = e.target;
            startX = e.clientX;
            startY = e.clientY;
            startWidth = layer.x;
            startHeight = layer.y;
            isResizing = target.classList.contains('resize');
            isRotating = target.classList.contains('rotate');
            if (isResizing) {
                startWidth = layer.width;
                startHeight = layer.height;
            }
            if (isRotating) {
                startRotation = layer.rotation;
            }
            document.addEventListener('pointermove', onPointerMove);
            document.addEventListener('pointerup', endPointer);
        });
    }

    function bindHistory() {
        root.querySelector('[data-action="undo"]').addEventListener('click', () => {
            if (state.history.length === 0) return;
            state.future.push(state.layers);
            state.layers = state.history.pop();
            refresh();
        });

        root.querySelector('[data-action="redo"]').addEventListener('click', () => {
            const next = state.future.pop();
            if (!next) return;
            state.history.push(state.layers);
            state.layers = next;
            refresh();
        });

        root.querySelector('[data-action="reset"]').addEventListener('click', () => {
            state.layers = [];
            pushHistory();
            refresh();
        });
    }

    function bindExports() {
        const exportScreen = () => {
            const url = canvas2d.toDataURL('image/png');
            download(url, 'winshirt-screen.png');
        };
        root.querySelector('[data-action="export-screen"]').addEventListener('click', exportScreen);
        root.querySelector('[data-action="quick-export"]').addEventListener('click', exportScreen);

        const exportPrint = () => {
            const url = canvas2d.toDataURL('image/png');
            download(url, 'winshirt-print.png');
        };
        root.querySelector('[data-action="export-print"]').addEventListener('click', exportPrint);
    }

    function download(url, filename) {
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
    }

    function bindCart() {
        const button = root.querySelector('[data-action="add-to-cart"]');
        if (!button) return;
        button.addEventListener('click', () => {
            const payload = {
                preview: canvas2d.toDataURL('image/png'),
                hd_file: canvas2d.toDataURL('image/png'),
                layers: state.layers,
                zone: state.face,
            };
            const field = document.createElement('textarea');
            field.name = 'winshirt_customizer_payload';
            field.hidden = true;
            field.value = JSON.stringify(payload);
            root.appendChild(field);
            const nonce = root.querySelector('input[name="winshirt_customizer_nonce"]');
            if (nonce) {
                nonce.setAttribute('name', 'winshirt_customizer_nonce');
            }
            button.closest('form')?.submit();
        });
    }

    function init3D() {
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(35, canvas3d.clientWidth / canvas3d.clientHeight, 0.1, 1000);
        camera.position.set(0, 1.5, 3);
        const renderer = new THREE.WebGLRenderer({ canvas: canvas3d, antialias: true, alpha: true });
        renderer.setSize(canvas3d.clientWidth, canvas3d.clientHeight);
        renderer.setPixelRatio(window.devicePixelRatio);

        const light = new THREE.DirectionalLight(0xffffff, 1);
        light.position.set(2, 3, 4);
        scene.add(light);
        scene.add(new THREE.AmbientLight(0xffffff, 0.4));

        const geometry = new THREE.BoxGeometry(1, 1.4, 0.6);
        const texture = new THREE.CanvasTexture(canvas2d);
        const material = new THREE.MeshStandardMaterial({ map: texture, roughness: 0.6, metalness: 0.05 });
        const mesh = new THREE.Mesh(geometry, material);
        scene.add(mesh);

        const controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enablePan = true;
        controls.enableDamping = true;

        function animate() {
            requestAnimationFrame(animate);
            controls.update();
            renderer.render(scene, camera);
        }

        animate();

        window.addEventListener('resize', () => {
            renderer.setSize(canvas3d.clientWidth, canvas3d.clientHeight);
            camera.aspect = canvas3d.clientWidth / canvas3d.clientHeight;
            camera.updateProjectionMatrix();
        });

        return texture;
    }

    const threePromise = new Promise((resolve) => {
        if (THREE.OrbitControls) {
            return resolve();
        }
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/three@0.165.0/examples/js/controls/OrbitControls.js';
        script.onload = resolve;
        document.head.appendChild(script);
    });

    let textureRef = null;
    function update3DTexture() {
        if (textureRef) {
            textureRef.needsUpdate = true;
        }
    }

    function restoreState() {
        const saved = localStorage.getItem('winshirt_customizer_state');
        if (!saved) return;
        try {
            const parsed = JSON.parse(saved);
            state.layers = parsed.layers || [];
            state.face = parsed.face || 'front';
        } catch (e) {
            console.warn('Restore failed', e);
        }
    }

    bindTabs();
    bindFaces();
    bindUploads();
    bindTextControls();
    bindQR();
    bindAI();
    bindHistory();
    bindExports();
    bindCart();
    restoreState();

    threePromise.then(() => {
        textureRef = init3D();
        refresh();
    });
})();
