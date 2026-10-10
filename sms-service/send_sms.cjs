#!/usr/bin/env node

const https = require('https');
const querystring = require('querystring');
const dns = require('dns');

// Configuration - Load from environment variables with fallbacks
// These will be set at module load time, but may be overridden in main() if passed as arguments
let API_KEY = process.env.SEMAPHORE_API_KEY || '';
let SENDER_NAME = process.env.SEMAPHORE_SENDER_NAME || 'Click2Serve';
const API_URL = process.env.SEMAPHORE_API_URL || 'https://api.semaphore.co/api/v4/messages';
const TEST_NUMBER = process.env.SEMAPHORE_TEST_NUMBER || '639219580125';
const USE_TEST_NUMBER = process.env.SEMAPHORE_USE_TEST_NUMBER === 'true';

// Function to update API key and sender name (called from main() if passed as arguments)
function updateConfig(apiKey, senderName) {
    if (apiKey) API_KEY = apiKey;
    if (senderName) SENDER_NAME = senderName;
}

function logMessage(message) {
    const timestamp = new Date().toISOString().replace('T', ' ').substr(0, 19);
    console.log(`[${timestamp}] ${message}`);
}

function sendMessage(message, number) {
    return new Promise((resolve, reject) => {
        try {
            logMessage('Sending Message...');

            // Validate API key again (in case it was cleared)
            if (!API_KEY || API_KEY.trim() === '') {
                const errorMsg = 'ERROR: SEMAPHORE_API_KEY is not set. Cannot send SMS.';
                logMessage(errorMsg);
                resolve({
                    success: false,
                    error: 'API key not configured',
                    data: errorMsg
                });
                return;
            }

            // Use test number if configured
            const targetNumber = USE_TEST_NUMBER ? TEST_NUMBER : number;
            if (USE_TEST_NUMBER) {
                logMessage(`Using test number: ${targetNumber} (original: ${number})`);
            }

            // Normalize phone number (remove +, spaces, dashes, ensure it starts with country code)
            let normalizedNumber = targetNumber.replace(/[\s\-\+]/g, '');
            // If number doesn't start with country code, assume it's a local number and add 63
            if (!normalizedNumber.startsWith('63') && normalizedNumber.length === 10) {
                normalizedNumber = '63' + normalizedNumber;
            } else if (normalizedNumber.startsWith('0') && normalizedNumber.length === 11) {
                normalizedNumber = '63' + normalizedNumber.substring(1);
            }

            // Semaphore API v4 accepts API key in query parameter OR POST body
            // We'll try query parameter first (more common for REST APIs)

            // Prepare POST body parameters
            const postParams = {
                message: message,
                number: normalizedNumber
            };

            // Add sender name if configured (Semaphore v4 supports sendername parameter)
            if (SENDER_NAME && SENDER_NAME.trim() !== '') {
                postParams.sendername = SENDER_NAME.trim();
            }

            logMessage(`API Key: ${API_KEY.substring(0, 8)}...${API_KEY.slice(-4)}`);
            logMessage(`Sender: ${SENDER_NAME || 'Not set'}`);
            logMessage(`Original Number: ${targetNumber}`);
            logMessage(`Normalized Number: ${normalizedNumber}`);
            logMessage(`Message: ${message.length > 50 ? message.substring(0, 50) + '...' : message}`);

            // Prepare the POST body data
            const postData = querystring.stringify(postParams);

            // Parse URL and add API key as query parameter (Semaphore standard)
            let url;
            try {
                url = new URL(API_URL);
            } catch (urlError) {
                logMessage(`Invalid API URL: ${API_URL}`);
                resolve({
                    success: false,
                    error: 'Invalid API URL',
                    data: `Failed to parse URL: ${API_URL}`
                });
                return;
            }

            url.searchParams.append('apikey', API_KEY);

            const fullPath = url.pathname + url.search;
            logMessage(`Request URL: ${url.protocol}//${url.hostname}${fullPath.replace(API_KEY, '***')}`);
            logMessage(`POST Data: ${postData.replace(API_KEY, '***')}`);

            // Validate hostname
            if (!url.hostname || url.hostname.trim() === '') {
                logMessage('ERROR: Invalid hostname in API URL');
                resolve({
                    success: false,
                    error: 'Invalid hostname',
                    data: 'API URL does not contain a valid hostname'
                });
                return;
            }

            // Helper function to make HTTPS request (defined before dns.lookup so it's in scope)
            function makeHttpsRequest(ipAddress, hostname) {
                // Now make the HTTPS request
                const options = {
                    hostname: hostname, // Keep hostname for SNI (Server Name Indication)
                    port: url.port || 443,
                    path: fullPath,
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Content-Length': Buffer.byteLength(postData),
                        'User-Agent': 'Click2Serve-SMS-Service/1.0',
                        'Host': hostname
                    },
                    timeout: 30000,
                    // Use IPv4 family
                    family: 4
                };

                const req = https.request(options, (res) => {
                    logMessage(`Response Status: ${res.statusCode}`);

                    let data = '';

                    res.on('data', (chunk) => {
                        data += chunk;
                    });

                    res.on('end', () => {
                        try {
                            logMessage(`Raw Response: ${data}`);

                            if (res.statusCode === 200 || res.statusCode === 201) {
                                let responseData;

                                // Try to parse JSON response
                                try {
                                    responseData = JSON.parse(data);
                                } catch (parseErr) {
                                    // If not JSON, check if it's a simple success message
                                    if (data.toLowerCase().includes('success') || data.toLowerCase().includes('queued')) {
                                        logMessage('Received success message (non-JSON)');
                                        resolve({
                                            success: true,
                                            message_id: null,
                                            status: 'queued',
                                            network: null,
                                            sender: SENDER_NAME || null,
                                            data: { raw_response: data }
                                        });
                                        return;
                                    }
                                    throw parseErr;
                                }

                                logMessage(`Response Data: ${JSON.stringify(responseData, null, 2)}`);

                                // Semaphore API v4 can return either an array or an object
                                let messageData;
                                if (Array.isArray(responseData) && responseData.length > 0) {
                                    messageData = responseData[0];
                                } else if (typeof responseData === 'object' && responseData !== null) {
                                    messageData = responseData;
                                } else {
                                    logMessage('Unexpected response format');
                                    resolve({
                                        success: false,
                                        error: 'Unexpected response format',
                                        data: responseData
                                    });
                                    return;
                                }

                                const messageId = messageData.message_id || messageData.id || messageData.messageId || null;
                                const status = messageData.status || 'queued';
                                const network = messageData.network || messageData.carrier || null;
                                const sender = messageData.sender_name || messageData.sendername || SENDER_NAME || null;

                                logMessage(`Message ID: ${messageId || 'N/A'}`);
                                logMessage(`Status: ${status}`);
                                logMessage(`Network: ${network || 'N/A'}`);
                                logMessage(`Sender: ${sender || 'N/A'}`);

                                // Return success response
                                resolve({
                                    success: true,
                                    message_id: messageId,
                                    status: status,
                                    network: network,
                                    sender: sender,
                                    data: responseData
                                });
                            } else {
                                // Try to parse error response
                                let errorData;
                                try {
                                    errorData = JSON.parse(data);
                                } catch (e) {
                                    errorData = { message: data };
                                }

                                logMessage(`HTTP Error: ${res.statusCode}`);
                                logMessage(`Error Response: ${JSON.stringify(errorData, null, 2)}`);

                                const errorMessage = errorData.message || errorData.error || `HTTP ${res.statusCode}`;

                                resolve({
                                    success: false,
                                    error: errorMessage,
                                    status_code: res.statusCode,
                                    data: errorData
                                });
                            }
                        } catch (parseError) {
                            logMessage(`Parse Error: ${parseError.message}`);
                            logMessage(`Invalid JSON response: ${data}`);
                            resolve({
                                success: false,
                                error: 'Invalid JSON response: ' + parseError.message,
                                data: data
                            });
                        }
                    });
                });

                req.on('error', (error) => {
                    logMessage(`Request error: ${error.code} - ${error.message}`);

                    if (error.code === 'ECONNRESET' || error.code === 'ETIMEDOUT') {
                        logMessage('Request timeout');
                        resolve({
                            success: false,
                            error: 'Request timeout',
                            data: 'Request timed out after 30 seconds'
                        });
                    } else if (error.code === 'ENOTFOUND' || error.code === 'EAI_FAIL' || error.code === 'EAI_AGAIN') {
                        // DNS resolution error (shouldn't happen since we pre-resolved, but handle it anyway)
                        logMessage(`DNS resolution failed for ${url.hostname}`);
                        logMessage(`Error details: ${error.message}`);
                        resolve({
                            success: false,
                            error: `DNS resolution failed: Cannot resolve ${url.hostname}. Check your internet connection and DNS settings.`,
                            error_code: error.code,
                            data: error.message
                        });
                    } else if (error.code === 'ECONNREFUSED') {
                        logMessage('Connection refused');
                        resolve({
                            success: false,
                            error: 'Connection refused',
                            data: 'Failed to connect to Semaphore API - connection refused'
                        });
                    } else {
                        logMessage(`Unexpected error: ${error.code} - ${error.message}`);
                        resolve({
                            success: false,
                            error: `Network error: ${error.message}`,
                            error_code: error.code,
                            data: error.message
                        });
                    }
                });

                req.on('timeout', () => {
                    req.destroy();
                    logMessage('Request timeout');
                    resolve({
                        success: false,
                        error: 'Request timeout',
                        data: 'Request timed out after 30 seconds'
                    });
                });

                // Send the request
                req.write(postData);
                req.end();
            } // End of makeHttpsRequest function

            // First, try to resolve DNS to ensure hostname is reachable
            // Use dns.resolve4() as a fallback if dns.lookup() fails (better for Windows)
            logMessage(`Resolving DNS for ${url.hostname}...`);

            // Try dns.lookup first (uses system DNS)
            dns.lookup(url.hostname, { family: 4, all: false }, (dnsError, address, family) => {
                if (dnsError) {
                    logMessage(`DNS lookup failed, trying dns.resolve4()... Error: ${dnsError.code} - ${dnsError.message}`);

                    // Fallback to dns.resolve4() which uses network DNS servers directly
                    dns.resolve4(url.hostname, (resolveError, addresses) => {
                        if (resolveError) {
                            logMessage(`DNS resolve4 also failed: ${resolveError.code} - ${resolveError.message}`);
                            resolve({
                                success: false,
                                error: `DNS resolution failed: Cannot resolve ${url.hostname}. Check your internet connection and DNS settings.`,
                                error_code: resolveError.code,
                                data: resolveError.message,
                                suggestion: 'Try running: nslookup api.semaphore.co to test DNS resolution'
                            });
                            return;
                        }

                        // Use the first resolved address
                        const resolvedAddress = addresses[0];
                        logMessage(`DNS resolved via resolve4: ${url.hostname} -> ${resolvedAddress}`);
                        makeHttpsRequest(resolvedAddress, url.hostname);
                    });
                    return;
                }

                logMessage(`DNS resolved via lookup: ${url.hostname} -> ${address}`);
                makeHttpsRequest(address, url.hostname);
            }); // End of dns.lookup callback
        } catch (error) {
            logMessage(`Unexpected error: ${error.message}`);
            resolve({
                success: false,
                error: 'Unexpected error',
                data: error.message
            });
        }
    });
}

function sendStatusUpdate(trackingNumber, status, fullName = null) {
    const statusLabels = {
        'inquiry': 'Inquiry',
        'to_be_encoded_brap': 'To Be Encoded to BRAP Request List',
        'checklist_given': 'Checklist of Requirements Given',
        'incomplete_requirements': 'Incomplete Requirements',
        'accepted': 'Accepted',
        'registered': 'Registered',
        'encoded_brap': 'Encoded to BRAP Request List',
        'negative_result': 'Negative Result',
        'positive_result': 'Positive Result',
        'rejected': 'Rejected'
    };

    const statusLabel = statusLabels[status] || status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

    // Special message for registered status
    if (status === 'registered') {
        const message = `Your request #${trackingNumber} is now ready to receive! Please visit our office to collect your document. Thank you for using our services!`;
        return sendMessage(message, '639219580125');
    }

    const message = `Your request #${trackingNumber} status has been updated to: ${statusLabel}. Thank you for using our services!`;
    return sendMessage(message, '639219580125');
}

// Main function for command line usage
function main() {
    const args = process.argv.slice(2);

    if (args.length < 2) {
        console.log('Usage: node send_sms.js <message> <number> [api_key] [sender_name]');
        console.log('Example: node send_sms.js "Hello World" "639277975100"');
        process.exit(1);
    }

    const message = args[0];
    const number = args[1];

    // Allow API key and sender name to be passed as command-line arguments (fallback)
    if (args.length >= 3 && args[2]) {
        updateConfig(args[2], null);
    }
    if (args.length >= 4 && args[3]) {
        updateConfig(null, args[3]);
    }

    // Validate API key
    if (!API_KEY || API_KEY.trim() === '') {
        console.error('ERROR: SEMAPHORE_API_KEY is not set. Please set it in environment variables or pass as 3rd argument.');
        process.exit(1);
    }

    sendMessage(message, number)
        .then(result => {
            if (result.success) {
                console.log('SUCCESS: Message sent successfully!');
                console.log(`Message ID: ${result.message_id || 'N/A'}`);
                console.log(`Status: ${result.status || 'N/A'}`);
                console.log(`Network: ${result.network || 'N/A'}`);
                console.log(`Sender: ${result.sender || 'N/A'}`);
                process.exit(0);
            } else {
                console.log('ERROR: Message failed to send!');
                console.log(`Error: ${result.error || 'Unknown error'}`);
                console.log(`Status Code: ${result.status_code || 'N/A'}`);
                console.log(`Details: ${JSON.stringify(result.data || {}, null, 2)}`);
                process.exit(1);
            }
        })
        .catch(error => {
            console.log('ERROR: Unexpected error occurred!');
            console.log(`Error: ${error.message}`);
            console.log(`Stack: ${error.stack || 'N/A'}`);
            process.exit(1);
        });
}

// Export functions for use as module
module.exports = {
    sendMessage,
    sendStatusUpdate
};

// Run main function if called directly
if (require.main === module) {
    main();
}
