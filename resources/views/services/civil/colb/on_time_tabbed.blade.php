<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>On-time COLB - Click2Serve</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/services.css') }}">
    <link rel="stylesheet" href="{{ asset('css/civil.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    <style>
        .tab-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .tab-navigation {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e0e0e0;
        }

        .tab-button {
            background: none;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: 500;
            color: #666;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }

        .tab-button.active {
            color: #0e90a6;
            border-bottom-color: #0e90a6;
        }

        .tab-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .tab-progress {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .progress-step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin: 0 10px;
            position: relative;
        }

        .progress-step.completed {
            background: #0e90a6;
            color: white;
        }

        .progress-step.active {
            background: #0e90a6;
            color: white;
            box-shadow: 0 0 0 4px rgba(14, 144, 166, 0.2);
        }

        .progress-line {
            width: 60px;
            height: 2px;
            background: #e0e0e0;
            position: absolute;
            top: 50%;
            left: 50px;
            transform: translateY(-50%);
        }

        .progress-line.completed {
            background: #0e90a6;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .tab-validation-message {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
    </style>
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
                    <h1 class="page-title">On-time Certificate of Live Birth (COLB)</h1>
                </div>

                <div class="main-content">
                    <div class="form-panel">
                        <h2 class="form-title">Certificate of Live Birth Request</h2>
                        <p
                            style="text-align: center; color: var(--text-secondary); margin-bottom: 30px; font-size: 16px;">
                            Please provide the required information for the Certificate of Live Birth
                        </p>

                        <div class="tab-container">
                            <!-- Progress Indicator -->
                            <div class="tab-progress">
                                <div class="progress-step active" id="step-1-indicator">
                                    <span>1</span>
                                </div>
                                <div class="progress-line" id="line-1-2"></div>
                                <div class="progress-step" id="step-2-indicator">
                                    <span>2</span>
                                </div>
                                <div class="progress-line" id="line-2-3"></div>
                                <div class="progress-step" id="step-3-indicator">
                                    <span>3</span>
                                </div>
                            </div>

                            <!-- Tab Navigation -->
                            <div class="tab-navigation">
                                <button class="tab-button active" data-tab="personal" id="personal-tab">
                                    Personal Information
                                </button>
                                <button class="tab-button" data-tab="mother" id="mother-tab" disabled>
                                    Mother's Information
                                </button>
                                <button class="tab-button" data-tab="father" id="father-tab" disabled>
                                    Father's Information
                                </button>
                            </div>

                            <!-- Tab Content -->
                            <form class="kiosk-form" id="colbTabbedForm">
                                <!-- Personal Information Tab -->
                                <div class="tab-content active" id="personal-content">
                                    <div class="section">
                                        <h3 class="section-title">Child's Personal Information</h3>
                                        <div class="grid-3">
                                            <div class="form-field">
                                                <label for="first_name">First Name</label>
                                                <input type="text" id="first_name" name="first_name"
                                                    placeholder="Enter first name" required>
                                            </div>
                                            <div class="form-field">
                                                <label for="middle_name">Middle Name</label>
                                                <input type="text" id="middle_name" name="middle_name"
                                                    placeholder="Enter middle name">
                                            </div>
                                            <div class="form-field">
                                                <label for="last_name">Last Name</label>
                                                <input type="text" id="last_name" name="last_name"
                                                    placeholder="Enter last name" required>
                                            </div>
                                            <div class="form-field">
                                                <label for="birth_date">Date of Birth</label>
                                                <input type="date" id="birth_date" name="birth_date" required>
                                            </div>
                                            <div class="form-field">
                                                <label for="place_of_birth">Place of Birth</label>
                                                <input type="text" id="place_of_birth" name="place_of_birth"
                                                    list="pobList" placeholder="Hospital/Clinic/Address" required>
                                                <datalist id="pobList">
                                                    <option value="Alaminos City RHU"></option>
                                                    <option value="Western Pangasinan District Hospital"></option>
                                                    <option value="Clinic"></option>
                                                    <option value="Home"></option>
                                                    <option value="Other"></option>
                                                </datalist>
                                            </div>
                                            <div class="form-field">
                                                <label for="child_sex">Sex</label>
                                                <select id="child_sex" name="child_sex" required>
                                                    <option value="" disabled selected>Select</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </div>
                                            <div class="form-field">
                                                <label for="birth_weight">Birth Weight (grams)</label>
                                                <input type="number" id="birth_weight" name="birth_weight" min="0"
                                                    placeholder="e.g. 3200">
                                            </div>
                                            <div class="form-field">
                                                <label for="birth_type">Type of Birth</label>
                                                <select id="birth_type" name="birth_type">
                                                    <option value="" disabled selected>Select</option>
                                                    <option value="Single">Single</option>
                                                    <option value="Twin">Twin</option>
                                                    <option value="Multiple">Multiple</option>
                                                </select>
                                            </div>
                                            <div class="form-field">
                                                <label for="birth_order">If Multiple</label>
                                                <input type="text" id="birth_order" name="birth_order"
                                                    placeholder="Birth order / notes">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="section">
                                        <h3 class="section-title">Contact Information</h3>
                                        <div class="grid-2">
                                            <div class="form-field">
                                                <label for="contact_number">Contact Number</label>
                                                <input type="tel" id="contact_number" name="contact_number"
                                                    placeholder="09xx xxx xxxx" required>
                                            </div>
                                            <div class="form-field">
                                                <label for="email">Email Address</label>
                                                <input type="email" id="email" name="email"
                                                    placeholder="your.email@example.com">
                                            </div>
                                            <div class="form-field full-width">
                                                <label for="address">Complete Address</label>
                                                <input type="text" id="address" name="address" list="addressList"
                                                    placeholder="Street, Barangay, City" required>
                                                <datalist id="addressList">
                                                    <option value="Poblacion, Alaminos City"></option>
                                                    <option value="Lucap, Alaminos City"></option>
                                                    <option value="San Jose, Alaminos City"></option>
                                                    <option value="San Vicente, Alaminos City"></option>
                                                    <option value="Other"></option>
                                                </datalist>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="section">
                                        <h3 class="section-title">Additional Information</h3>
                                        <div class="form-field">
                                            <label for="additional_info">Additional Information</label>
                                            <textarea id="additional_info" name="additional_info"
                                                placeholder="Any additional details or special requests..."
                                                rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mother's Information Tab -->
                                <div class="tab-content" id="mother-content">
                                    <div class="section">
                                        <h3 class="section-title">Mother's Information</h3>
                                        <div class="grid-3">
                                            <div class="form-field">
                                                <label for="mother_first_name">First Name</label>
                                                <input type="text" id="mother_first_name" name="mother_first_name"
                                                    placeholder="Enter first name" required>
                                            </div>
                                            <div class="form-field">
                                                <label for="mother_middle_name">Middle Name</label>
                                                <input type="text" id="mother_middle_name" name="mother_middle_name"
                                                    placeholder="Enter middle name">
                                            </div>
                                            <div class="form-field">
                                                <label for="mother_last_name">Last Name</label>
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
                                                <label for="mother_residence">Complete Residence</label>
                                                <input type="text" id="mother_residence" name="mother_residence"
                                                    placeholder="Address">
                                            </div>
                                            <div class="form-field">
                                                <label for="mother_birthday">Birthday</label>
                                                <input type="date" id="mother_birthday" name="mother_birthday">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Father's Information Tab -->
                                <div class="tab-content" id="father-content">
                                    <div class="section">
                                        <h3 class="section-title">Father's Information</h3>
                                        <div class="grid-3">
                                            <div class="form-field">
                                                <label for="father_first_name">First Name</label>
                                                <input type="text" id="father_first_name" name="father_first_name"
                                                    placeholder="Enter first name">
                                            </div>
                                            <div class="form-field">
                                                <label for="father_middle_name">Middle Name</label>
                                                <input type="text" id="father_middle_name" name="father_middle_name"
                                                    placeholder="Enter middle name">
                                            </div>
                                            <div class="form-field">
                                                <label for="father_last_name">Last Name</label>
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
                                                <label for="father_residence">Complete Residence</label>
                                                <input type="text" id="father_residence" name="father_residence"
                                                    placeholder="Address">
                                            </div>
                                            <div class="form-field">
                                                <label for="father_birthday">Birthday</label>
                                                <input type="date" id="father_birthday" name="father_birthday">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab Validation Message -->
                                <div class="tab-validation-message" id="tab-validation-message">
                                    Please complete all required fields in the current tab before proceeding.
                                </div>

                                <!-- Form Actions -->
                                <div class="form-actions">
                                    <button type="button" class="secondary-btn" onclick="goBack()">Cancel</button>
                                    <div>
                                        <button type="button" class="secondary-btn" id="prev-tab-btn"
                                            style="display: none;">Previous</button>
                                        <button type="button" class="primary-btn" id="next-tab-btn">Next</button>
                                        <button type="submit" class="primary-btn" id="submit-btn"
                                            style="display: none;">Submit Request</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <button class="chat" aria-label="chat" onclick="navigateToChatbot()">
                <img src="{{ asset('assets/icons/chatbot.png') }}" alt="chat" />
            </button>
            <div class="footer">Powered by: Smart Municipal Kiosk</div>
        </div>
    </div>

    <!-- Datalists -->
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
        let currentTab = 1;
        const totalTabs = 3;

        function goBack() {
            window.location.href = '/services/civil';
        }

        function navigateToChatbot() {
            window.location.href = '/chatbot';
        }

        // Tab navigation
        function switchTab(tabNumber) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });

            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });

            // Show selected tab content
            const tabNames = ['personal', 'mother', 'father'];
            document.getElementById(tabNames[tabNumber - 1] + '-content').classList.add('active');
            document.getElementById(tabNames[tabNumber - 1] + '-tab').classList.add('active');

            // Update progress indicators
            updateProgressIndicators(tabNumber);

            // Update navigation buttons
            updateNavigationButtons(tabNumber);

            currentTab = tabNumber;
        }

        function updateProgressIndicators(activeTab) {
            for (let i = 1; i <= totalTabs; i++) {
                const indicator = document.getElementById(`step-${i}-indicator`);
                const line = document.getElementById(`line-${i}-${i + 1}`);

                if (i < activeTab) {
                    indicator.classList.add('completed');
                    if (line) line.classList.add('completed');
                } else if (i === activeTab) {
                    indicator.classList.add('active');
                    indicator.classList.remove('completed');
                } else {
                    indicator.classList.remove('active', 'completed');
                }
            }
        }

        function updateNavigationButtons(activeTab) {
            const prevBtn = document.getElementById('prev-tab-btn');
            const nextBtn = document.getElementById('next-tab-btn');
            const submitBtn = document.getElementById('submit-btn');

            if (activeTab === 1) {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'inline-block';
                submitBtn.style.display = 'none';
            } else if (activeTab === totalTabs) {
                prevBtn.style.display = 'inline-block';
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'inline-block';
            } else {
                prevBtn.style.display = 'inline-block';
                nextBtn.style.display = 'inline-block';
                submitBtn.style.display = 'none';
            }
        }

        function validateCurrentTab() {
            const currentTabContent = document.querySelector('.tab-content.active');
            const requiredFields = currentTabContent.querySelectorAll('input[required], select[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (field.value.trim() === '') {
                    field.parentElement.classList.add('error');
                    isValid = false;
                } else {
                    field.parentElement.classList.remove('error');
                    field.parentElement.classList.add('success');
                }
            });

            return isValid;
        }

        function showTabValidationMessage() {
            const message = document.getElementById('tab-validation-message');
            message.style.display = 'block';
            setTimeout(() => {
                message.style.display = 'none';
            }, 3000);
        }

        // Event listeners
        document.getElementById('next-tab-btn').addEventListener('click', function () {
            if (validateCurrentTab()) {
                if (currentTab < totalTabs) {
                    switchTab(currentTab + 1);
                }
            } else {
                showTabValidationMessage();
            }
        });

        document.getElementById('prev-tab-btn').addEventListener('click', function () {
            if (currentTab > 1) {
                switchTab(currentTab - 1);
            }
        });

        // Tab button clicks
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function () {
                if (!this.disabled) {
                    const tabNumber = parseInt(this.dataset.tab === 'personal' ? '1' :
                        this.dataset.tab === 'mother' ? '2' : '3');
                    switchTab(tabNumber);
                }
            });
        });

        // Form submission
        document.getElementById('colbTabbedForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            if (!validateCurrentTab()) {
                showTabValidationMessage();
                return;
            }

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            // Combine names for full name
            const childName = `${data.first_name} ${data.middle_name} ${data.last_name}`.trim();
            const motherName = `${data.mother_first_name || ''} ${data.mother_middle_name || ''} ${data.mother_last_name || ''}`.trim();
            const fatherName = `${data.father_first_name || ''} ${data.father_middle_name || ''} ${data.father_last_name || ''}`.trim();

            // Prepare data for API
            const requestData = {
                full_name: childName,
                document_type: 'Certificate of Live Birth (On-time)',
                contact_number: data.contact_number,
                email: data.email,
                address: data.address,
                additional_info: data.additional_info,
                child_info: {
                    name: childName,
                    birth_date: data.birth_date,
                    place_of_birth: data.place_of_birth,
                    sex: data.child_sex,
                    birth_weight: data.birth_weight,
                    birth_type: data.birth_type,
                    birth_order: data.birth_order
                },
                mother_info: {
                    name: motherName,
                    citizenship: data.mother_citizenship,
                    religion: data.mother_religion,
                    occupation: data.mother_occupation,
                    age: data.mother_age,
                    residence: data.mother_residence,
                    birthday: data.mother_birthday
                },
                father_info: {
                    name: fatherName,
                    citizenship: data.father_citizenship,
                    religion: data.father_religion,
                    occupation: data.father_occupation,
                    age: data.father_age,
                    residence: data.father_residence,
                    birthday: data.father_birthday
                },
                contact_info: {
                    person: childName,
                    number: data.contact_number,
                    email: data.email,
                    address: data.address
                }
            };

            // Show loading state
            const submitBtn = this.querySelector('#submit-btn');
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
                        contact_number: data.contact_number,
                        email: data.email,
                        address: data.address,
                        print_status: result.print_status
                    };

                    // Store tracking data for the tracker page
                    sessionStorage.setItem('currentTrackingData', JSON.stringify(trackingData));

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

        // Phone number formatting
        document.getElementById('contact_number').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0 && value.length <= 11) {
                if (value.length <= 4) {
                    value = value;
                } else if (value.length <= 7) {
                    value = value.slice(0, 4) + ' ' + value.slice(4);
                } else {
                    value = value.slice(0, 4) + ' ' + value.slice(4, 7) + ' ' + value.slice(7);
                }
            }
            e.target.value = value;
        });

        // Auto-focus first field for kiosk use
        document.addEventListener('DOMContentLoaded', function () {
            const firstInput = document.querySelector('input[required]');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 500);
            }
        });

        // Add CSRF token meta tag
        if (!document.querySelector('meta[name="csrf-token"')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }
    </script>
</body>

</html>
