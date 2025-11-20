(function ($) {
    function bindRepeatables() {
        $('[data-add-row]').off('click').on('click', function (e) {
            e.preventDefault();
            const target = $(this).data('add-row');
            const table = $(`table[data-repeatable="${target}"] tbody`);
            const index = table.children().length;
            const template = table.children().first().clone(true);
            template.find('input').each(function () {
                const name = $(this).attr('name');
                $(this).val('');
                if (name) {
                    $(this).attr('name', name.replace(/\[\d+\]/, `[${index}]`));
                }
            });
            template.find('select').each(function () {
                const name = $(this).attr('name');
                $(this).val($(this).find('option:first').val());
                if (name) {
                    $(this).attr('name', name.replace(/\[\d+\]/, `[${index}]`));
                }
            });
            template.find('.winshirt-3d-config').attr('data-zones', '[]').removeAttr('data-bound');
            template.find('[data-zone-input]').val('[]');
            template.find('[data-zone-list]').empty();
            table.append(template);
            bindRemovers();
            bindMediaUploaders();
            init3DMockups(template[0]);
        });
    }

    function bindRemovers() {
        $('.winshirt-remove').off('click').on('click', function (e) {
            e.preventDefault();
            const row = $(this).closest('tr');
            if (row.siblings().length === 0) {
                row.find('input, select').val('');
            } else {
                row.remove();
            }
        });
    }

    function bindMediaUploaders() {
        $('.winshirt-upload-3d').off('click').on('click', function (e) {
            e.preventDefault();
            const button = $(this);
            const input = button.closest('td').find('input[type="url"]');
            const frame = wp.media({
                title: 'Sélectionner un fichier 3D',
                button: { text: 'Utiliser ce fichier' },
                multiple: false,
                library: {
                    type: ['model/gltf-binary', 'model/gltf+json', 'model/obj']
                }
            });

            frame.on('select', function () {
                const attachment = frame.state().get('selection').first().toJSON();
                if (attachment && attachment.url) {
                    input.val(attachment.url).trigger('change');
                }
            });

            frame.open();
        });
    }

    $(document).ready(function () {
        bindRepeatables();
        bindRemovers();
        bindMediaUploaders();
        init3DMockups();
    });

    const availableZones = (() => {
        const wrapper = document.querySelector('.winshirt-admin');
        if (!wrapper) return [];
        try {
            return JSON.parse(wrapper.dataset.availableZones || '[]');
        } catch (e) {
            return [];
        }
    })();

    let threeExtrasPromise = null;
    function loadThreeExtras() {
        if (threeExtrasPromise) return threeExtrasPromise;

        threeExtrasPromise = new Promise((resolve) => {
            const scripts = [];
            if (!THREE.OrbitControls) {
                scripts.push('https://cdn.jsdelivr.net/npm/three@0.165.0/examples/js/controls/OrbitControls.js');
            }
            if (!THREE.GLTFLoader) {
                scripts.push('https://cdn.jsdelivr.net/npm/three@0.165.0/examples/js/loaders/GLTFLoader.js');
            }
            if (!THREE.OBJLoader) {
                scripts.push('https://cdn.jsdelivr.net/npm/three@0.165.0/examples/js/loaders/OBJLoader.js');
            }

            if (scripts.length === 0) {
                resolve();
                return;
            }

            let loaded = 0;
            scripts.forEach((src) => {
                const tag = document.createElement('script');
                tag.src = src;
                tag.onload = () => {
                    loaded += 1;
                    if (loaded === scripts.length) {
                        resolve();
                    }
                };
                document.head.appendChild(tag);
            });
        });

        return threeExtrasPromise;
    }

    function clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    function init3DMockups(scope = document) {
        loadThreeExtras().then(() => {
            const configs = scope.querySelectorAll('.winshirt-3d-config');
            configs.forEach((config) => {
                if (config.dataset.bound === '1') return;
                config.dataset.bound = '1';
                setupMockup3D(config);
            });
        });
    }

    function setupMockup3D(config) {
        const canvas = config.querySelector('.winshirt-3d-canvas');
        const overlay = config.querySelector('[data-zone-overlay]');
        const zoneInput = config.querySelector('[data-zone-input]');
        const zoneList = config.querySelector('[data-zone-list]');
        const zoneSelect = config.querySelector('[data-zone-select]');
        const addZoneBtn = config.querySelector('[data-add-zone]');
        const row = config.closest('tr');
        const fileInput = row?.querySelector('input[name*="[file]"]');
        const textureInput = row?.querySelector('input[name*="[texture]"]');

        let zones = [];
        try {
            zones = JSON.parse(zoneInput.value || '[]');
            if (!Array.isArray(zones)) zones = [];
        } catch (e) {
            zones = [];
        }

        const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(35, canvas.clientWidth / canvas.clientHeight, 0.1, 1000);
        camera.position.set(0, 1.5, 3);
        const light = new THREE.DirectionalLight(0xffffff, 1);
        light.position.set(2, 3, 4);
        scene.add(light);
        scene.add(new THREE.AmbientLight(0xffffff, 0.4));

        const controls = new THREE.OrbitControls(camera, canvas);
        controls.enableDamping = true;
        controls.target.set(0, 0.8, 0);

        let mesh = null;
        function setMesh(object) {
            if (mesh) {
                scene.remove(mesh);
            }
            mesh = object;
            scene.add(mesh);
        }

        function applyMaterial(object, material) {
            if (object.traverse) {
                object.traverse((child) => {
                    if (child.isMesh) {
                        child.material = material.clone();
                        child.material.needsUpdate = true;
                    }
                });
            }
        }

        function buildMaterial() {
            const material = new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.6, metalness: 0.05 });
            const textureUrl = textureInput?.value?.trim();
            if (textureUrl) {
                new THREE.TextureLoader().load(textureUrl, (tex) => {
                    tex.flipY = false;
                    material.map = tex;
                    material.needsUpdate = true;
                });
            }
            return material;
        }

        function fallbackMesh(material) {
            const geometry = new THREE.BoxGeometry(1, 1.4, 0.6);
            return new THREE.Mesh(geometry, material);
        }

        function loadModel() {
            const url = fileInput?.value?.trim();
            const material = buildMaterial();

            if (!url) {
                setMesh(fallbackMesh(material));
                return;
            }

            const extension = url.split('.').pop()?.toLowerCase();
            const onError = () => setMesh(fallbackMesh(material));

            if (extension === 'glb' || extension === 'gltf') {
                const loader = new THREE.GLTFLoader();
                loader.load(url, (gltf) => {
                    const model = gltf.scene;
                    applyMaterial(model, material);
                    setMesh(model);
                }, undefined, onError);
                return;
            }

            if (extension === 'obj') {
                const loader = new THREE.OBJLoader();
                loader.load(url, (obj) => {
                    applyMaterial(obj, material);
                    setMesh(obj);
                }, undefined, onError);
                return;
            }

            setMesh(fallbackMesh(material));
        }

        function resizeRenderer() {
            renderer.setSize(canvas.clientWidth, canvas.clientHeight);
            camera.aspect = canvas.clientWidth / canvas.clientHeight;
            camera.updateProjectionMatrix();
        }

        function animate() {
            requestAnimationFrame(animate);
            controls.update();
            renderer.render(scene, camera);
        }

        resizeRenderer();
        animate();
        loadModel();

        window.addEventListener('resize', resizeRenderer);

        let dragState = null;
        let markers = [];

        function persistZones() {
            zoneInput.value = JSON.stringify(zones);
        }

        function renderZoneList() {
            if (!zoneList) return;
            zoneList.innerHTML = '';
            zones.forEach((zone, index) => {
                const item = document.createElement('div');
                item.className = 'winshirt-zone-item';
                const label = document.createElement('span');
                label.textContent = zone.label || zone.id || `Zone ${index + 1}`;
                const face = document.createElement('span');
                face.className = 'face';
                face.textContent = zone.face || '';
                const remove = document.createElement('button');
                remove.type = 'button';
                remove.textContent = '✕';
                remove.addEventListener('click', () => {
                    zones.splice(index, 1);
                    renderZones();
                });
                item.append(label, face, remove);
                zoneList.appendChild(item);
            });
        }

        function renderZones() {
            overlay.innerHTML = '';
            markers = [];
            zones.forEach((zone, index) => {
                const marker = document.createElement('div');
                marker.className = 'winshirt-zone-marker';
                marker.dataset.index = String(index);
                marker.style.width = `${clamp(zone.width || 0.25, 0.05, 1) * 100}%`;
                marker.style.height = `${clamp(zone.height || 0.25, 0.05, 1) * 100}%`;
                marker.style.left = `${clamp(zone.x || 0, 0, 1) * 100}%`;
                marker.style.top = `${clamp(zone.y || 0, 0, 1) * 100}%`;

                const title = document.createElement('span');
                title.className = 'label';
                title.textContent = zone.label || zone.id || 'Zone';
                marker.appendChild(title);
                overlay.appendChild(marker);
                markers.push(marker);
            });

            renderZoneList();
            persistZones();
        }

        function startDrag(event) {
            const target = event.target.closest('.winshirt-zone-marker');
            if (!target) return;
            const rect = overlay.getBoundingClientRect();
            const index = parseInt(target.dataset.index, 10);
            const zone = zones[index];
            dragState = {
                index,
                startX: event.clientX,
                startY: event.clientY,
                rect,
                startPos: {
                    x: zone.x,
                    y: zone.y,
                },
            };
            event.preventDefault();
        }

        function moveDrag(event) {
            if (!dragState) return;
            const zone = zones[dragState.index];
            const rect = dragState.rect;
            const deltaX = event.clientX - dragState.startX;
            const deltaY = event.clientY - dragState.startY;
            const newX = clamp(dragState.startPos.x + deltaX / rect.width, 0, 1 - (zone.width || 0.25));
            const newY = clamp(dragState.startPos.y + deltaY / rect.height, 0, 1 - (zone.height || 0.25));
            zone.x = newX;
            zone.y = newY;
            const marker = markers[dragState.index];
            if (marker) {
                marker.style.left = `${newX * 100}%`;
                marker.style.top = `${newY * 100}%`;
            }
        }

        function stopDrag() {
            if (!dragState) return;
            dragState = null;
            persistZones();
        }

        overlay.addEventListener('mousedown', startDrag);
        window.addEventListener('mousemove', moveDrag);
        window.addEventListener('mouseup', stopDrag);

        addZoneBtn?.addEventListener('click', () => {
            const selected = zoneSelect?.value;
            if (!selected) return;
            const available = availableZones.find((z) => z.name === selected) || {};
            const baseWidth = 0.35;
            const ratio = available.width && available.height ? available.height / available.width : 1;
            const width = clamp(baseWidth, 0.1, 0.65);
            const height = clamp(baseWidth * ratio, 0.1, 0.65);
            zones.push({
                id: available.name || selected,
                label: available.name || selected,
                face: available.face || '',
                x: 0.3,
                y: 0.2,
                width,
                height,
            });
            renderZones();
        });

        fileInput?.addEventListener('change', loadModel);
        textureInput?.addEventListener('change', loadModel);

        renderZones();
    }
})(jQuery);
