<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>On-time COLB - Step 2 - Click2Serve</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
    <link rel="stylesheet" href="{{ asset('css/civil.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
</head>

<body>
    <div class="viewport">
        <div class="stage">
            <div class="wrapper">
                <div class="header">
                    <button class="back-button" onclick="goBack()">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                    <h1 class="page-title">On-time Certificate of Live Birth (COLB) - Step 2</h1>
                </div>

                <div class="main-content">
                    <div class="form-panel">
                        <h2 class="form-title">Detailed Information Sheet</h2>
                        <p
                            style="text-align: center; color: var(--text-secondary); margin-bottom: 30px; font-size: 16px;">
                            Please provide detailed information for the Certificate of
                            Live Birth
                        </p>

                        <form class="kiosk-form" id="detailedColbForm">
                            <div class="section">
                                <h3 class="section-title">Child's Information</h3>
                                <div class="grid-3">
                                    <div class="form-field">
                                        <label for="child_first_name">First
                                            Name</label>
                                        <input type="text" id="child_first_name" name="child_first_name"
                                            placeholder="Enter first name" required>
                                    </div>
                                    <div class="form-field">
                                        <label for="child_middle_name">Middle
                                            Name</label>
                                        <input type="text" id="child_middle_name" name="child_middle_name"
                                            placeholder="Enter middle name">
                                    </div>
                                    <div class="form-field">
                                        <label for="child_last_name">Last
                                            Name</label>
                                        <input type="text" id="child_last_name" name="child_last_name"
                                            placeholder="Enter last name" required>
                                    </div>
                                    <div class="form-field">
                                        <label for="child_birth_date">Date of
                                            Birth</label>
                                        <input type="date" id="child_birth_date" name="child_birth_date" required>
                                    </div>
                                    <div class="form-field">
                                        <label for="place_of_birth">Place of
                                            Birth</label>
                                        <input type="text" id="place_of_birth" name="place_of_birth" list="pobList"
                                            placeholder="Hospital/Clinic/Address" required>
                                        <datalist id="pobList">
                                            <option value="Alaminos City RHU"></option>
                                            <option value="Western Pangasinan District Hospital"></option>
                                            <option value="Clinic"></option>
                                            <option value="Home"></option>
                                            <option value="Other"></option>
                                        </datalist>
                                    </div>
                                    <div class="form-field">
                                        <label for="birth_weight">Birth Weight
                                            (grams)</label>
                                        <input type="number" id="birth_weight" name="birth_weight" min="0"
                                            placeholder="e.g. 3200">
                                    </div>
                                    <div class="form-field">
                                        <label for="child_sex">Sex</label>
                                        <select id="child_sex" name="child_sex" required>
                                            <option value="" disabled selected>Select</option>
                                            <option value="Male">Male
                                            </option>
                                            <option value="Female">Female
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-field">
                                        <label for="birth_type">Type of
                                            Birth</label>
                                        <select id="birth_type" name="birth_type">
                                            <option value="" disabled selected>Select</option>
                                            <option value="Single">Single
                                            </option>
                                            <option value="Twin">Twin
                                            </option>
                                            <option value="Multiple">
                                                Multiple</option>
                                        </select>
                                    </div>
                                    <div class="form-field">
                                        <label for="birth_order">If
                                            Multiple</label>
                                        <input type="text" id="birth_order" name="birth_order"
                                            placeholder="Birth order / notes">
                                    </div>
                                </div>
                            </div>

                            <div class="section">
                                <h3 class="section-title">Mother's Information</h3>
                                <div class="grid-3">
                                    <div class="form-field">
                                        <label for="mother_first_name">First
                                            Name</label>
                                        <input type="text" id="mother_first_name" name="mother_first_name"
                                            placeholder="Enter first name" required>
                                    </div>
                                    <div class="form-field">
                                        <label for="mother_middle_name">Middle
                                            Name</label>
                                        <input type="text" id="mother_middle_name" name="mother_middle_name"
                                            placeholder="Enter middle name">
                                    </div>
                                    <div class="form-field">
                                        <label for="mother_last_name">Last
                                            Name</label>
                                        <input type="text" id="mother_last_name" name="mother_last_name"
                                            placeholder="Enter last name" required>
                                    </div>
                                    <div class="form-field">
                                        <label for="mother_citizenship">Citizenship</label>
                                        <input type="text" id="mother_citizenship" name="mother_citizenship"
                                            list="citizenshipList" placeholder="Citizenship">
                                    </div>
                                    <div class="form-field">
                                        <label for="mother_religion">Religion</label>
                                        <input type="text" id="mother_religion" name="mother_religion"
                                            list="religionList" placeholder="Religion">
                                    </div>
                                    <div class="form-field">
                                        <label for="mother_occupation">Occupation</label>
                                        <input type="text" id="mother_occupation" name="mother_occupation"
                                            placeholder="Occupation">
                                    </div>
                                    <div class="form-field">
                                        <label for="mother_age">Age</label>
                                        <input type="number" id="mother_age" name="mother_age" min="0"
                                            placeholder="Age">
                                    </div>
                                    <div class="form-field">
                                        <label for="mother_residence">Complete
                                            Residence</label>
                                        <input type="text" id="mother_residence" name="mother_residence"
                                            placeholder="Address">
                                    </div>
                                    <div class="form-field">
                                        <label for="mother_birthday">Birthday</label>
                                        <input type="date" id="mother_birthday" name="mother_birthday">
                                    </div>
                                </div>
                            </div>

                            <div class="section">
                                <h3 class="section-title">Father's Information</h3>
                                <div class="grid-3">
                                    <div class="form-field">
                                        <label for="father_first_name">First
                                            Name</label>
                                        <input type="text" id="father_first_name" name="father_first_name"
                                            placeholder="Enter first name">
                                    </div>
                                    <div class="form-field">
                                        <label for="father_middle_name">Middle
                                            Name</label>
                                        <input type="text" id="father_middle_name" name="father_middle_name"
                                            placeholder="Enter middle name">
                                    </div>
                                    <div class="form-field">
                                        <label for="father_last_name">Last
                                            Name</label>
                                        <input type="text" id="father_last_name" name="father_last_name"
                                            placeholder="Enter last name">
                                    </div>
                                    <div class="form-field">
                                        <label for="father_citizenship">Citizenship</label>
                                        <input type="text" id="father_citizenship" name="father_citizenship"
                                            list="citizenshipList" placeholder="Citizenship">
                                    </div>
                                    <div class="form-field">
                                        <label for="father_religion">Religion</label>
                                        <input type="text" id="father_religion" name="father_religion"
                                            list="religionList" placeholder="Religion">
                                    </div>
                                    <div class="form-field">
                                        <label for="father_occupation">Occupation</label>
                                        <input type="text" id="father_occupation" name="father_occupation"
                                            placeholder="Occupation">
                                    </div>
                                    <div class="form-field">
                                        <label for="father_age">Age</label>
                                        <input type="number" id="father_age" name="father_age" min="0"
                                            placeholder="Age">
                                    </div>
                                    <div class="form-field">
                                        <label for="father_residence">Complete
                                            Residence</label>
                                        <input type="text" id="father_residence" name="father_residence"
                                            placeholder="Address">
                                    </div>
                                    <div class="form-field">
                                        <label for="father_birthday">Birthday</label>
                                        <input type="date" id="father_birthday" name="father_birthday">
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="secondary-btn" onclick="goBack()">Back</button>
                                <button type="submit" class="primary-btn">Submit
                                    Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <button class="chat" aria-label="chat" onclick="navigateToChatbot()">
                <img src="{{ asset('assets/icons/chatbot.png') }}" alt="chat" />
            </button>
            <div class="footer">Powered by: Smart Municipal Kiosk</div>
        </div>
    </div>
    <datalist id="citizenshipList">
        <option value="Filipino"></option>
        <option value="American"></option>
        <option value="Chinese"></option>
        <option value="Japanese"></option>
        <option value="Other"></option>
    </datalist>
    <datalist id="religionList">
        <option value="Roman Catholic"></option>
        <option value="Iglesia ni Cristo"></option>
        <option value="Protestant"></option>
        <option value="Muslim"></option>
        <option value="Other"></option>
    </datalist>

    <script>
        function goBack() {
            window.location.href = '/services/civil/colb/on-time';
        }

        function navigateToChatbot() {
            window.location.href = '/chatbot';
        }

        // Load Step 1 data on page load
        document.addEventListener('DOMContentLoaded', function () {
            const step1Data = sessionStorage.getItem('colbStep1Data');

            if (!step1Data) {
                // If no step 1 data, redirect back to step 1
                alert('Please complete Step 1 first.');
                window.location.href = '/services/civil/colb/on-time';
                return;
            }

            const data = JSON.parse(step1Data);

            // Pre-populate child information from step 1
            document.getElementById('child_first_name').value = data.first_name || '';
            document.getElementById('child_middle_name').value = data.middle_name || '';
            document.getElementById('child_last_name').value = data.last_name || '';
            document.getElementById('child_birth_date').value = data.birth_date || '';
            document.getElementById('place_of_birth').value = data.place_of_birth || '';

            // Restore Step 2 data if available (in case user goes back and returns)
            const step2SavedData = sessionStorage.getItem('colbStep2Data');
            if (step2SavedData) {
                const savedData = JSON.parse(step2SavedData);
                Object.keys(savedData).forEach(key => {
                    const field = document.getElementById(key) || document.querySelector(`[name="${key}"]`);
                    if (field && savedData[key]) {
                        field.value = savedData[key];
                    }
                });
            }

            // Auto-focus first empty field for kiosk use
            const firstEmpty = document.querySelector('input:not([value]):not([type="hidden"])');
            if (firstEmpty) {
                setTimeout(() => firstEmpty.focus(), 500);
            }
        });

        // Form submission with thermal printer integration
        document.getElementById('detailedColbForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const step2Data = Object.fromEntries(formData);

            // Get step 1 data
            const step1Data = JSON.parse(sessionStorage.getItem('colbStep1Data') || '{}');

            // Combine names for full name
            const childName = `${step2Data.child_first_name} ${step2Data.child_middle_name} ${step2Data.child_last_name}`.trim();
            const motherName = `${step2Data.mother_first_name} ${step2Data.mother_middle_name} ${step2Data.mother_last_name}`.trim();

            // Prepare data for API (combining step 1 and step 2 data)
            const requestData = {
                full_name: childName,
                document_type: 'Certificate of Live Birth (On-time)',
                contact_number: step1Data.contact_number,
                email: step1Data.email,
                address: step1Data.address,
                purpose: step1Data.purpose,
                additional_info: step1Data.additional_info,
                // Detailed information from both steps
                child_info: {
                    name: childName,
                    birth_date: step2Data.child_birth_date,
                    place_of_birth: step2Data.place_of_birth,
                    sex: step2Data.child_sex,
                    birth_weight: step2Data.birth_weight,
                    birth_type: step2Data.birth_type,
                    birth_order: step2Data.birth_order
                },
                mother_info: {
                    name: motherName,
                    citizenship: step2Data.mother_citizenship,
                    religion: step2Data.mother_religion,
                    occupation: step2Data.mother_occupation,
                    age: step2Data.mother_age,
                    residence: step2Data.mother_residence,
                    birthday: step2Data.mother_birthday
                },
                father_info: {
                    name: `${step2Data.father_first_name} ${step2Data.father_middle_name} ${step2Data.father_last_name}`.trim(),
                    citizenship: step2Data.father_citizenship,
                    religion: step2Data.father_religion,
                    occupation: step2Data.father_occupation,
                    age: step2Data.father_age,
                    residence: step2Data.father_residence,
                    birthday: step2Data.father_birthday
                },
                // Original step 1 contact information
                contact_info: {
                    person: step1Data.first_name + ' ' + step1Data.last_name,
                    number: step1Data.contact_number,
                    email: step1Data.email,
                    address: step1Data.address
                }
            };

            // Show loading state
            const submitBtn = this.querySelector('.primary-btn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Processing...';
            submitBtn.disabled = true;

            try {
                const response = await fetch('/api/request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(requestData)
                });

                const result = await response.json();

                if (result.success) {
                    // Prepare tracking data for the tracker page
                    const trackingData = {
                        full_name: childName,
                        document_type: 'Certificate of Live Birth (On-time)',
                        tracking_number: result.tracking_number,
                        queue_number: result.queue_number,
                        status: 'submitted',
                        date: new Date().toLocaleDateString(),
                        time: new Date().toLocaleTimeString(),
                        purpose: step1Data.purpose,
                        contact_number: step1Data.contact_number,
                        email: step1Data.email,
                        address: step1Data.address,
                        print_status: result.print_status
                    };

                    // Store tracking data for the tracker page
                    sessionStorage.setItem('currentTrackingData', JSON.stringify(trackingData));

                    // Clear form data
                    sessionStorage.removeItem('colbStep1Data');
                    sessionStorage.removeItem('colbStep2Data');

                    // Bubble notification
                    (function () {
                        const overlay = document.createElement('div');
                        overlay.style.position = 'fixed'; overlay.style.inset = '0'; overlay.style.display = 'grid'; overlay.style.placeItems = 'center'; overlay.style.background = 'rgba(0,0,0,0)'; overlay.style.zIndex = '9999';
                        const bubble = document.createElement('div');
                        bubble.style.padding = '18px 24px'; bubble.style.borderRadius = '16px'; bubble.style.boxShadow = '0 18px 40px rgba(0,0,0,.28)'; bubble.style.fontFamily = 'Poppins,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif'; bubble.style.fontWeight = '600'; bubble.style.fontSize = '18px'; bubble.style.color = '#fff'; bubble.style.background = '#0e90a6'; bubble.textContent = 'Request submitted! Printing receipt...';
                        overlay.appendChild(bubble); document.body.appendChild(overlay); setTimeout(() => overlay.remove(), 3000);
                    })();

                    // Redirect to tracker with URL parameters as backup
                    window.location.href = `/services/tracker?tracking=${result.tracking_number}&queue=${result.queue_number}`;
                } else {
                    (function () {
                        const o = document.createElement('div'); o.style.position = 'fixed'; o.style.inset = '0'; o.style.display = 'grid'; o.style.placeItems = 'center'; o.style.background = 'rgba(0,0,0,0)'; o.style.zIndex = '9999';
                        const b = document.createElement('div'); b.style.padding = '18px 24px'; b.style.borderRadius = '16px'; b.style.boxShadow = '0 18px 40px rgba(0,0,0,.28)'; b.style.fontFamily = 'Poppins,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif'; b.style.fontWeight = '600'; b.style.fontSize = '18px'; b.style.color = '#fff'; b.style.background = '#dc3545'; b.textContent = 'Error submitting request'; o.appendChild(b); document.body.appendChild(o); setTimeout(() => o.remove(), 3000);
                    })();
                }
            } catch (error) {
                (function () {
                    const o = document.createElement('div'); o.style.position = 'fixed'; o.style.inset = '0'; o.style.display = 'grid'; o.style.placeItems = 'center'; o.style.background = 'rgba(0,0,0,0)'; o.style.zIndex = '9999';
                    const b = document.createElement('div'); b.style.padding = '18px 24px'; b.style.borderRadius = '16px'; b.style.boxShadow = '0 18px 40px rgba(0,0,0,.28)'; b.style.fontFamily = 'Poppins,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif'; b.style.fontWeight = '600'; b.style.fontSize = '18px'; b.style.color = '#fff'; b.style.background = '#dc3545'; b.textContent = 'Network error'; o.appendChild(b); document.body.appendChild(o); setTimeout(() => o.remove(), 3000);
                })();
            } finally {
                // Reset button
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });

        // Add CSRF token meta tag
        if (!document.querySelector('meta[name="csrf-token"')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }

        // Form validation enhancements
        document.querySelectorAll('input[required], select[required]').forEach(field => {
            field.addEventListener('blur', function () {
                if (this.value.trim() === '') {
                    this.parentElement.classList.add('error');
                } else {
                    this.parentElement.classList.remove('error');
                    this.parentElement.classList.add('success');
                }
            });
        });

        // Auto-save form data in case user needs to go back
        document.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', function () {
                const currentStep2Data = {};
                const formData = new FormData(document.getElementById('detailedColbForm'));
                for (let [key, value] of formData.entries()) {
                    currentStep2Data[key] = value;
                }
                sessionStorage.setItem('colbStep2Data', JSON.stringify(currentStep2Data));
            });
        });
    </script>
</body>

</html>