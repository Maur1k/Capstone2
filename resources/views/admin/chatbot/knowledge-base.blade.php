<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dialogflow Knowledge Base - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/super-admin.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/chatbot-admin.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">Dialogflow Knowledge Base</h1>
                    <p class="header-subtitle">View and manage chatbot knowledge base documents</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="stats-grid" id="statsGrid">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-database"></i>
                </div>
                <h3 id="totalDocuments">-</h3>
                <p>Total Documents</p>
            </div>
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 id="activeDocuments">-</h3>
                <p>Active Documents</p>
            </div>
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-language"></i>
                </div>
                <h3 id="languageCode">-</h3>
                <p>Language Code</p>
            </div>
        </div>

        <div class="documents-table">
            <div class="table-header">
                <h2>Knowledge Base Documents</h2>
                <div class="flex-gap-12">
                    <button class="refresh-btn" id="refreshBtn" onclick="loadKnowledgeBase()">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                    <button class="add-btn" onclick="openAddModal()">
                        <i class="fas fa-plus"></i>
                        Add Document
                    </button>
                    <button class="add-btn btn-emerald" onclick="openImportModal()">
                        <i class="fas fa-file-csv"></i>
                        Import CSV
                    </button>
                </div>
            </div>
            <div id="contentArea">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading knowledge base...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Import CSV Modal -->
    <div id="importModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Import CSV File</h2>
                <button class="close-btn" onclick="closeImportModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="importSuccessMessage" class="success-message"></div>
            <form id="importCsvForm" onsubmit="importCsv(event)" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="csvFile">CSV File *</label>
                    <input type="file" id="csvFile" name="csv_file" accept=".csv,.txt" required>
                    <small>CSV file must have "Question" and "Answer" columns. Maximum file size: 10MB</small>
                </div>
                <div class="form-group">
                    <label for="importDisplayName">Document Name (Optional)</label>
                    <input type="text" id="importDisplayName" name="displayName" placeholder="e.g., Click2Serve FAQs">
                    <small>Leave empty to use auto-generated name</small>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeImportModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="importSubmitBtn">
                        <i class="fas fa-file-upload"></i> Import CSV
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Document Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Document</h2>
                <button class="close-btn" onclick="closeAddModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="successMessage" class="success-message"></div>
            <form id="addDocumentForm" onsubmit="createDocument(event)">
                <div class="form-group">
                    <label for="displayName">Document Name *</label>
                    <input type="text" id="displayName" name="displayName" required
                        placeholder="e.g., Business Permit FAQ">
                    <small>The display name for this document</small>
                </div>
                <div class="form-group">
                    <label for="mimeType">MIME Type *</label>
                    <select id="mimeType" name="mimeType" required>
                        <option value="text/plain">Plain Text (text/plain)</option>
                        <option value="text/html">HTML (text/html)</option>
                        <option value="text/csv">CSV (text/csv)</option>
                        <option value="application/pdf">PDF (application/pdf)</option>
                    </select>
                    <small>Select the content type of your document</small>
                </div>
                <div class="form-group">
                    <label for="knowledgeTypes">Knowledge Types *</label>
                    <select id="knowledgeTypes" name="knowledgeTypes[]" multiple required>
                        <option value="FAQ">FAQ (Frequently Asked Questions)</option>
                        <option value="EXTRACTIVE_QA">Extractive Q&A</option>
                        <option value="ARTICLE_SUGGESTION">Article Suggestion</option>
                    </select>
                    <small>Hold Ctrl/Cmd to select multiple types. FAQ is recommended for chatbot responses.</small>
                </div>
                <div class="form-group">
                    <label for="content">Content *</label>
                    <textarea id="content" name="content" required
                        placeholder="Enter the document content here. For FAQ format, use Q: Question&#10;A: Answer format."></textarea>
                    <small>For FAQ format, use: Q: Question followed by A: Answer on separate lines</small>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-plus"></i> Create Document
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        async function loadKnowledgeBase() {
            const refreshBtn = document.getElementById('refreshBtn');
            const contentArea = document.getElementById('contentArea');

            refreshBtn.disabled = true;
            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            contentArea.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i><p>Loading knowledge base...</p></div>';

            try {
                const response = await fetch('/chatbot/knowledge-base/scan');
                const data = await response.json();

                if (data.success) {
                    displayKnowledgeBase(data);
                } else {
                    contentArea.innerHTML = `<div class="error"><i class="fas fa-exclamation-circle"></i> ${data.error || 'Failed to load knowledge base'}</div>`;
                }
            } catch (error) {
                contentArea.innerHTML = `<div class="error"><i class="fas fa-exclamation-circle"></i> Error: ${error.message}</div>`;
            } finally {
                refreshBtn.disabled = false;
                refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
            }
        }

        function displayKnowledgeBase(data) {
            // Update stats
            document.getElementById('totalDocuments').textContent = data.totalDocuments || 0;
            document.getElementById('activeDocuments').textContent = data.documents.filter(d => d.state === 'ACTIVE' || d.state === 'CREATING').length || 0;
            document.getElementById('languageCode').textContent = data.knowledgeBase?.languageCode?.toUpperCase() || 'N/A';

            // Display documents table
            const contentArea = document.getElementById('contentArea');

            if (!data.documents || data.documents.length === 0) {
                contentArea.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No documents found in the knowledge base.</p>
                    </div>
                `;
                return;
            }

            let tableHTML = `
                <table>
                    <thead>
                        <tr>
                            <th>Document Name</th>
                            <th>MIME Type</th>
                            <th>Knowledge Types</th>
                            <th>State</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.documents.forEach(doc => {
                const stateBadge = getStateBadge(doc.state);
                const knowledgeTypes = doc.knowledgeTypes && doc.knowledgeTypes.length > 0
                    ? doc.knowledgeTypes.join(', ')
                    : 'N/A';
                const createdDate = doc.createTime ? new Date(doc.createTime).toLocaleDateString() : 'N/A';
                const updatedDate = doc.updateTime ? new Date(doc.updateTime).toLocaleDateString() : 'N/A';
                const canDelete = doc.state === 'ACTIVE' || doc.state === 'CREATING';

                tableHTML += `
                    <tr>
                        <td><strong>${escapeHtml(doc.displayName)}</strong></td>
                        <td>${escapeHtml(doc.mimeType)}</td>
                        <td>${escapeHtml(knowledgeTypes)}</td>
                        <td>${stateBadge}</td>
                        <td>${createdDate}</td>
                        <td>${updatedDate}</td>
                        <td>
                            ${canDelete ? `<button class="delete-btn" onclick="deleteDocument('${doc.id}', '${escapeHtml(doc.displayName)}')">
                                <i class="fas fa-trash"></i> Delete
                            </button>` : '<span class="text-gray-400">-</span>'}
                        </td>
                    </tr>
                `;
            });

            tableHTML += `
                    </tbody>
                </table>
            `;

            contentArea.innerHTML = tableHTML;
        }

        function getStateBadge(state) {
            const stateMap = {
                'ACTIVE': { class: 'badge-success', text: 'Active' },
                'CREATING': { class: 'badge-warning', text: 'Creating' },
                'DELETING': { class: 'badge-warning', text: 'Deleting' },
                'RELOADING': { class: 'badge-info', text: 'Reloading' },
            };

            const badge = stateMap[state] || { class: 'badge-info', text: state || 'Unknown' };
            return `<span class="badge ${badge.class}">${badge.text}</span>`;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Modal functions
        function openAddModal() {
            document.getElementById('addModal').classList.add('show');
            document.getElementById('addDocumentForm').reset();
            document.getElementById('successMessage').classList.remove('show');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.remove('show');
        }

        function openImportModal() {
            document.getElementById('importModal').classList.add('show');
            document.getElementById('importCsvForm').reset();
            document.getElementById('importSuccessMessage').classList.remove('show');
        }

        function closeImportModal() {
            document.getElementById('importModal').classList.remove('show');
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const addModal = document.getElementById('addModal');
            const importModal = document.getElementById('importModal');
            if (event.target === addModal) {
                closeAddModal();
            }
            if (event.target === importModal) {
                closeImportModal();
            }
        }

        // Create document
        async function createDocument(event) {
            event.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const successMessage = document.getElementById('successMessage');
            const form = document.getElementById('addDocumentForm');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
            successMessage.classList.remove('show');

            const formData = new FormData(form);
            const knowledgeTypes = Array.from(document.getElementById('knowledgeTypes').selectedOptions)
                .map(option => option.value);

            try {
                const response = await fetch('/chatbot/knowledge-base/document', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        displayName: formData.get('displayName'),
                        mimeType: formData.get('mimeType'),
                        knowledgeTypes: knowledgeTypes,
                        content: formData.get('content')
                    })
                });

                const data = await response.json();

                if (data.success) {
                    successMessage.textContent = 'Document created successfully! It may take a few moments to become active.';
                    successMessage.className = 'success-message show';
                    form.reset();

                    // Reload knowledge base after 2 seconds
                    setTimeout(() => {
                        loadKnowledgeBase();
                        setTimeout(closeAddModal, 500);
                    }, 2000);
                } else {
                    successMessage.textContent = 'Error: ' + (data.error || 'Failed to create document');
                    successMessage.className = 'success-message show';
                    successMessage.style.background = '#fee2e2';
                    successMessage.style.color = '#991b1b';
                }
            } catch (error) {
                successMessage.textContent = 'Error: ' + error.message;
                successMessage.className = 'success-message show';
                successMessage.style.background = '#fee2e2';
                successMessage.style.color = '#991b1b';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-plus"></i> Create Document';
            }
        }

        // Delete document
        async function deleteDocument(documentId, documentName) {
            if (!confirm(`Are you sure you want to delete "${documentName}"? This action cannot be undone.`)) {
                return;
            }

            try {
                const response = await fetch(`/chatbot/knowledge-base/document/${documentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('Document deleted successfully!');
                    loadKnowledgeBase();
                } else {
                    alert('Error: ' + (data.error || 'Failed to delete document'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // Import CSV
        async function importCsv(event) {
            event.preventDefault();

            const submitBtn = document.getElementById('importSubmitBtn');
            const successMessage = document.getElementById('importSuccessMessage');
            const form = document.getElementById('importCsvForm');
            const fileInput = document.getElementById('csvFile');

            if (!fileInput.files || !fileInput.files[0]) {
                alert('Please select a CSV file');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
            successMessage.classList.remove('show');

            const formData = new FormData(form);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            try {
                const response = await fetch('/admin/chatbot/knowledge-base/import-csv', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    successMessage.textContent = `Successfully imported ${data.stats?.importedRecords || 0} FAQ entries! The document is being processed and may take a few moments to become active.`;
                    successMessage.className = 'success-message show';
                    form.reset();

                    // Reload knowledge base after 3 seconds
                    setTimeout(() => {
                        loadKnowledgeBase();
                        setTimeout(closeImportModal, 500);
                    }, 3000);
                } else {
                    successMessage.textContent = 'Error: ' + (data.error || 'Failed to import CSV');
                    successMessage.className = 'success-message show';
                    successMessage.style.background = '#fee2e2';
                    successMessage.style.color = '#991b1b';
                }
            } catch (error) {
                successMessage.textContent = 'Error: ' + error.message;
                successMessage.className = 'success-message show';
                successMessage.style.background = '#fee2e2';
                successMessage.style.color = '#991b1b';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-file-upload"></i> Import CSV';
            }
        }

        // Load on page load
        document.addEventListener('DOMContentLoaded', loadKnowledgeBase);
    </script>
</body>

</html>